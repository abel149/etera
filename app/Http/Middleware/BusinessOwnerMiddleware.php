<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class BusinessOwnerMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login again!');
        }

        if (Auth::user()->role !== \App\Models\User::ROLE_BUSINESS_OWNER) {
            return redirect('/login')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
