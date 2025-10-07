<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Admin;
use App\Models\Auditor;  
use App\Models\User;
use App\Models\Author;

class AuthController extends Controller
{
    /**
     * Show the unified login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle unified login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // Debug info
        \Log::info('Login attempt', $credentials);

        // Try to find user in Users table (unified system)
        $user = \App\Models\User::where('email', $credentials['email'])->first();
        
        if ($user && \Hash::check($credentials['password'], $user->password)) {
            // Login using web guard (unified auth)
            Auth::guard('web')->login($user, $remember);
            $request->session()->regenerate();
            
            \Log::info('User login successful', ['user_id' => $user->id, 'role' => $user->role]);
            
            // Redirect based on role
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'auditor':
                    return redirect()->route('auditor.dashboard');
                case 'author':
                    return redirect()->route('author.dashboard');
                default:
                    return redirect()->route('user.views');
            }
        }

        // If user not found in Users table, check legacy tables
        // Check admin table (legacy)
        $admin = \App\Models\Admin::where('email', $credentials['email'])->first();
        if ($admin && \Hash::check($credentials['password'], $admin->password)) {
            // Create or update user in unified table
            $unifiedUser = \App\Models\User::updateOrCreate(
                ['email' => $admin->email],
                [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'password' => $admin->password,
                    'role' => 'admin'
                ]
            );
            
            Auth::guard('web')->login($unifiedUser, $remember);
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        // Check auditor table (legacy)
        $auditor = \App\Models\Auditor::where('email', $credentials['email'])->first();
        if ($auditor && \Hash::check($credentials['password'], $auditor->password)) {
            // Create or update user in unified table
            $unifiedUser = \App\Models\User::updateOrCreate(
                ['email' => $auditor->email],
                [
                    'id' => $auditor->id,
                    'name' => $auditor->name,
                    'password' => $auditor->password,
                    'role' => 'auditor'
                ]
            );
            
            Auth::guard('web')->login($unifiedUser, $remember);
            $request->session()->regenerate();
            return redirect()->route('auditor.dashboard');
        }

        // Check author table (legacy)
        $author = \App\Models\Author::where('email', $credentials['email'])->first();
        if ($author && \Hash::check($credentials['password'], $author->password)) {
            // Create or update user in unified table
            $unifiedUser = \App\Models\User::updateOrCreate(
                ['email' => $author->email],
                [
                    'id' => $author->id,
                    'name' => $author->name,
                    'password' => $author->password,
                    'role' => 'author'
                ]
            );
            
            Auth::guard('web')->login($unifiedUser, $remember);
            $request->session()->regenerate();
            return redirect()->route('author.dashboard');
        }

        // If none worked, return error
        return back()->withErrors([
            'email' => 'Email atau password tidak valid.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout for unified auth system
     */
    public function logout(Request $request)
    {
        // Logout from web guard (unified auth)
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('auth.login');
    }

    /**
     * Show the register form for authors
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle author registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:authors,email|unique:users,email|unique:admins,email|unique:auditors,email',
            'password' => ['required', 'confirmed', Password::min(6)],
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'terms' => 'required|accepted',
        ], [
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
        ]);

        try {
            // Generate author ID
            $lastAuthor = Author::orderBy('created_at', 'desc')->first();
            $authorId = 'AUTHOR003'; // Start after existing seed data
            
            if ($lastAuthor) {
                $lastNumber = (int) substr($lastAuthor->id, 6);
                $newNumber = $lastNumber + 1;
                $authorId = 'AUTHOR' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            }

            // Generate corresponding user ID
            $userId = str_replace('AUTHOR', 'USER', $authorId);

            // Create author
            $author = Author::create([
                'id' => $authorId,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'bio' => 'Penulis di platform Blogger',
                'specialization' => 'General',
                'total_posts' => 0,
                'status' => 'active',
            ]);

            // Also create user record for authentication
            $user = User::create([
                'id' => $userId,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'author',
                'is_active' => 1,
            ]);

            Log::info('New author registered', [
                'author_id' => $author->id,
                'user_id' => $user->id,
                'email' => $request->email
            ]);

            // Auto login after registration
            Auth::guard('web')->login($user);
            $request->session()->regenerate();

            return redirect()->route('author.dashboard')->with('success', 'Registrasi berhasil! Selamat datang di platform Blogger.');

        } catch (\Exception $e) {
            Log::error('Author registration failed', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return back()->withInput()->withErrors([
                'email' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.'
            ]);
        }
    }
}
