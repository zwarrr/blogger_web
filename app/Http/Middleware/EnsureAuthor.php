<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('web')->check() || auth('web')->user()->role !== 'author') {
            return redirect()->route('auth.login');
        }
        return $next($request);
    }
}
