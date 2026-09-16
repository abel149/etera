<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class MarketerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            // Log session state so we can diagnose why the user is not authenticated.
            Log::warning('MarketerMiddleware: Auth::check() failed — session may have been cleared', [
                'url'            => $request->fullUrl(),
                'session_id'     => Session::getId(),
                'session_keys'   => array_keys(Session::all()),
                'has_activity'   => Session::has('last_activity'),
                'last_activity'  => Session::get('last_activity'),
                'time_now'       => time(),
                'ip'             => $request->ip(),
            ]);
            return redirect('/login')->with('error', 'Please login again!');
        }

        if (Auth::user()->role !== 'marketer') {
            Log::warning('MarketerMiddleware: role mismatch', [
                'user_id' => Auth::id(),
                'role'    => Auth::user()->role,
                'url'     => $request->fullUrl(),
            ]);
            Auth::logout();
            return redirect('/login')->with('error', 'Please login again!');
        }

        // Keep the session alive so AuthenticateUser's 30-min expiry check
        // (triggered by background polling on auth.user routes) never fires
        // and flushes the session while the marketer is actively navigating.
        Session::put('last_activity', time());

        return $next($request);
    }
}
