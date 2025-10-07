<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;
use App\Models\Auditor;  
use App\Models\Author;

class UnifiedAuthController extends Controller
{
    /**
     * Show the unified login form
     */
    public function showLoginForm()
    {
        return view('auth.unified-login');
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

        // Check admin (email: admin@gmail.com, password: admin987)
        $admin = \App\Models\Admin::where('email', $credentials['email'])->first();
        if ($admin && \Hash::check($credentials['password'], $admin->password)) {
            Auth::guard('admin')->login($admin, $remember);
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        // Check auditor
        $auditor = \App\Models\Auditor::where('email', $credentials['email'])->first();
        if ($auditor && \Hash::check($credentials['password'], $auditor->password)) {
            Auth::guard('auditor')->login($auditor, $remember);
            $request->session()->regenerate();
            return redirect()->route('auditor.dashboard');
        }

        // Check author
        $author = \App\Models\Author::where('email', $credentials['email'])->first();
        if ($author && \Hash::check($credentials['password'], $author->password)) {
            Auth::guard('author')->login($author, $remember);
            $request->session()->regenerate();
            return redirect()->route('author.dashboard');
        }

        // If none worked, return error
        return back()->withErrors([
            'email' => 'Email atau password tidak valid.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout for any guard
     */
    public function logout(Request $request)
    {
        // Check which guard is authenticated and logout from that guard
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } elseif (Auth::guard('auditor')->check()) {
            Auth::guard('auditor')->logout();
        } elseif (Auth::guard('author')->check()) {
            Auth::guard('author')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('auth.login');
    }
}
