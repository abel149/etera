<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login again!');
        }

        if (!in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            return redirect('/login')->with('error', 'Please login again!');
        }

        return $next($request);
    }
}
