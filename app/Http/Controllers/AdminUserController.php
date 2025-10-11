<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Auditor;
use App\Models\Author;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{
    public function index()
    {
        // Combine all user types
        $users = collect();
        
        // Get admins
        $admins = Admin::orderBy('created_at', 'desc')->get()->map(fn($u) => [
            'id' => $u->admin_id ?? $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => 'admin',
            'type' => 'admin'
        ]);
        
        // Get auditors
        $auditors = Auditor::orderBy('created_at', 'desc')->get()->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => 'auditor',
            'type' => 'auditor'
        ]);
        
        // Get authors
        $authors = Author::orderBy('created_at', 'desc')->get()->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => 'author',
            'type' => 'author'
        ]);
        
        $users = $admins->concat($auditors)->concat($authors);

        return view('admin.manage-user', ['users' => $users]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255'],
            'role' => ['required','in:admin,auditor,author'],
            'password' => ['required','string','min:6'],
        ]);

        // Create user based on role in appropriate table
        switch ($data['role']) {
            case 'admin':
                // Generate admin ID: ADMN001, ADMN002, etc.
                $lastAdmin = Admin::orderBy('admin_id', 'desc')->value('admin_id');
                $num = $lastAdmin ? intval(substr($lastAdmin, 4)) : 0;
                $adminId = 'ADMN' . str_pad((string)($num + 1), 1, '0', STR_PAD_LEFT);
                
                Admin::create([
                    'admin_id' => $adminId,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);
                break;
                
            case 'auditor':
                // Generate auditor ID: AUDITOR001, AUDITOR002, etc.
                $lastAuditor = Auditor::orderBy('id', 'desc')->value('id');
                $num = $lastAuditor ? intval(substr($lastAuditor, 7)) : 0;
                $auditorId = 'AUDITOR' . str_pad((string)($num + 1), 3, '0', STR_PAD_LEFT);
                
                Auditor::create([
                    'id' => $auditorId,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'status' => 'active',
                ]);
                break;
                
            case 'author':
                // Generate author ID: AUTHOR001, AUTHOR002, etc.
                $lastAuthor = Author::orderBy('id', 'desc')->value('id');
                $num = $lastAuthor ? intval(substr($lastAuthor, 6)) : 0;
                $authorId = 'AUTHOR' . str_pad((string)($num + 1), 3, '0', STR_PAD_LEFT);
                
                Author::create([
                    'id' => $authorId,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'status' => 'active',
                ]);
                break;
        }

        return back()->with('status', ucfirst($data['role']) . ' created successfully.');
    }

    public function update(Request $request, string $id)
    {
        try {
            // Find the user based on the ID format and role
            $user = null;
            $role = null;
            
            // Determine role based on ID format
            if (str_starts_with($id, 'ADMN')) {
                $user = Admin::where('admin_id', $id)->first();
                $role = 'admin';
            } elseif (str_starts_with($id, 'AUDITOR')) {
                $user = Auditor::where('id', $id)->first();
                $role = 'auditor';
            } elseif (str_starts_with($id, 'AUTHOR')) {
                $user = Author::where('id', $id)->first();
                $role = 'author';
            }

            if (!$user) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found.'
                    ], 404);
                }
                return back()->withErrors(['error' => 'User not found.']);
            }

            $data = $request->validate([
                'name' => ['required','string','max:255'],
                'email' => ['required','email','max:255'],
                'password' => ['nullable','string','min:6'],
            ]);

        // Check email uniqueness across tables (excluding current user)
        $emailExists = false;
        if ($role !== 'admin' && Admin::where('email', $data['email'])->where('admin_id', '!=', $id)->exists()) {
            $emailExists = true;
        }
        if ($role !== 'auditor' && Auditor::where('email', $data['email'])->where('id', '!=', $id)->exists()) {
            $emailExists = true;
        }
        if ($role !== 'author' && Author::where('email', $data['email'])->where('id', '!=', $id)->exists()) {
            $emailExists = true;
        }

        if ($emailExists) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email already exists.'
                ], 422);
            }
            return back()->withErrors(['email' => 'Email already exists.']);
        }

        // Update user data
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        // Only update password if provided
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

            $user->update($updateData);

            // Check if it's AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => ucfirst($role) . ' updated successfully.'
                ]);
            }

            return back()->with('status', ucfirst($role) . ' updated successfully.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                $errors = [];
                foreach ($e->errors() as $field => $messages) {
                    $errors[] = implode(', ', $messages);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $errors)
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating user.'
                ], 500);
            }
            return back()->withErrors(['error' => 'An error occurred while updating user.']);
        }
    }

    public function destroy(string $id)
    {
        try {
            // Find the user based on the ID format and role
            $user = null;
            $role = null;
            
            // Determine role based on ID format
            if (str_starts_with($id, 'ADMN')) {
                $user = Admin::where('admin_id', $id)->first();
                $role = 'admin';
            } elseif (str_starts_with($id, 'AUDITOR')) {
                $user = Auditor::where('id', $id)->first();
                $role = 'auditor';
            } elseif (str_starts_with($id, 'AUTHOR')) {
                $user = Author::where('id', $id)->first();
                $role = 'author';
            }

            if (!$user) {
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'User not found.'], 404);
                }
                return back()->withErrors(['error' => 'User not found.']);
            }

            // Prevent deletion of current admin
            if ($role === 'admin' && auth('admin')->check() && auth('admin')->user()->admin_id === $user->admin_id) {
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Cannot delete your own admin account.'], 403);
                }
                return back()->withErrors(['error' => 'Cannot delete your own admin account.']);
            }

            // Store user info for response
            $userName = $user->name;
            
            // Delete related user from users table if exists
            if ($role === 'auditor') {
                $userNumber = substr($user->id, 7); // Extract number from AUDITOR001
                User::where('id', 'USER' . $userNumber)->delete();
            } elseif ($role === 'author') {
                $userNumber = substr($user->id, 6); // Extract number from AUTHOR001
                User::where('id', 'USER' . $userNumber)->delete();
            }
            
            // Delete the user
            $user->delete();
            
            // Auto-arrange IDs after deletion
            $this->autoArrangeIds($role);

            $message = ucfirst($role) . ' "' . $userName . '" deleted successfully.';
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            
            return back()->with('status', $message);
            
        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'An error occurred while deleting the user.'], 500);
            }
            
            return back()->withErrors(['error' => 'An error occurred while deleting the user.']);
        }
    }

    /**
     * Auto-arrange IDs after deletion to maintain sequential numbering
     */
    private function autoArrangeIds($role)
    {
        try {
            switch ($role) {
                case 'admin':
                    $admins = Admin::orderBy('created_at', 'asc')->get();
                    foreach ($admins as $index => $admin) {
                        $newId = 'ADMN' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                        if ($admin->admin_id !== $newId) {
                            $admin->admin_id = $newId;
                            $admin->save();
                        }
                    }
                    break;
                    
                case 'auditor':
                    $auditors = Auditor::orderBy('created_at', 'asc')->get();
                    foreach ($auditors as $index => $auditor) {
                        $newId = 'AUDITOR' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                        if ($auditor->id !== $newId) {
                            $oldId = $auditor->id;
                            $auditor->id = $newId;
                            $auditor->save();
                            
                            // Update related users table if exists
                            $oldUserNumber = substr($oldId, 7); // Extract number from AUDITOR001
                            $newUserNumber = str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                            \App\Models\User::where('id', 'USER' . $oldUserNumber)
                                          ->update(['id' => 'USER' . $newUserNumber]);
                        }
                    }
                    break;
                    
                case 'author':
                    $authors = Author::orderBy('created_at', 'asc')->get();
                    foreach ($authors as $index => $author) {
                        $newId = 'AUTHOR' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                        if ($author->id !== $newId) {
                            $author->id = $newId;
                            $author->save();
                        }
                    }
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error auto-arranging IDs for ' . $role . ': ' . $e->getMessage());
        }
    }
}
