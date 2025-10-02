<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')
            ->get(['id','name','email','role'])
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
            ]);

        return view('admin.manage-user', ['users' => $users]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'role' => ['required','in:admin,editor,user'],
            'password' => ['required','string','min:6'],
        ]);

        // Generate custom incremental ID: USERBLOG001
        $prefix = 'USERBLOG';
        $last = User::where('id', 'like', $prefix.'%')
            ->orderBy('id', 'desc')
            ->value('id');
        $num = 0;
        if ($last && preg_match('/^'.preg_quote($prefix, '/').'([0-9]{3,})$/', $last, $m)) {
            $num = intval($m[1]);
        }
        $next = $prefix . str_pad((string)($num + 1), 3, '0', STR_PAD_LEFT);

        User::create([
            'id' => $next,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('status', 'User created successfully.');
    }
}
