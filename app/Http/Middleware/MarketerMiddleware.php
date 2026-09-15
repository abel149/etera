<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MarketerMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login again!');
        }

        if (Auth::user()->role !== 'marketer') {
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
