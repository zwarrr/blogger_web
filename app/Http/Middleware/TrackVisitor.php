<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\VisitorStat;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start session if not started
        if (!$request->hasSession()) {
            $request->setLaravelSession(app('session.store'));
        }
        
        // Get or create session ID
        $sessionId = $request->session()->getId();
        
        // Track this visitor
        VisitorStat::trackVisitor(
            $sessionId,
            $request->ip(),
            $request->userAgent()
        );
        
        // Clean old visitors occasionally (1% chance)
        if (rand(1, 100) === 1) {
            VisitorStat::cleanOldVisitors();
        }
        
        return $next($request);
    }
}
