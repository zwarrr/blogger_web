<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthorAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        // Generate custom incremental ID: USERBLOG001 (reuse logic similar to AdminUserController)
        $prefix = 'USERBLOG';
        $last = User::where('id', 'like', $prefix.'%')->orderBy('id', 'desc')->value('id');
        $num = 0;
        if ($last && preg_match('/^'.preg_quote($prefix, '/').'([0-9]{3,})$/', $last, $m)) {
            $num = intval($m[1]);
        }
        $next = $prefix . str_pad((string)($num + 1), 3, '0', STR_PAD_LEFT);

        $user = User::create([
            'id' => $next,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'author',
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->intended('/author/dashboard');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        // Use default web guard, but ensure role author
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            if (Auth::user()->role === 'author') {
                return redirect()->intended('/author/dashboard');
            }
            Auth::logout();
        }

        return back()->withErrors(['email' => 'Email atau password salah, atau bukan akun author.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('author.login');
    }
}
