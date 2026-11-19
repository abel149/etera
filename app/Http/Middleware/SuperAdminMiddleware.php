<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Only superadmin can perform this action.');
        }

        return $next($request);
    }
}
