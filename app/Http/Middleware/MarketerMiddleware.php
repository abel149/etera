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
            // Log full session state so we can see exactly why login_web_* is missing.
            Log::warning('MarketerMiddleware: Auth::check() failed — session may have been cleared', [
                'url'            => $request->fullUrl(),
                'session_id'     => Session::getId(),
                'session_keys'   => array_keys(Session::all()),
                'has_activity'   => Session::has('last_activity'),
                'last_activity'  => Session::get('last_activity'),
                'time_now'       => time(),
                'flash'          => Session::get('_flash'),
                'previous_url'   => Session::get('_previous.url'),
                'telegram_skip'  => Session::get('telegram_skipped'),
                'ip'             => $request->ip(),
            ]);
            return redirect('/login')->with('error', 'Please login again!');
        }

        if (Auth::user()->role !== 'marketer') {
            Log::warning('MarketerMiddleware: role mismatch — blocking access but NOT destroying session', [
                'user_id' => Auth::id(),
                'role'    => Auth::user()->role,
                'url'     => $request->fullUrl(),
            ]);
            // Do NOT call Auth::logout() here — that would remove login_web_* from the
            // session and make every subsequent page navigate look like "not authenticated".
            // Simply redirect; the user remains authenticated so they can reach their
            // own dashboard if the role is genuinely different, or retry if it was a
            // transient DB fetch issue.
            return redirect('/login')->with('error', 'Please login again!');
        }

        // Keep the session alive so AuthenticateUser's 30-min expiry check
        // (triggered by background polling on auth.user routes) never fires
        // and flushes the session while the marketer is actively navigating.
        Session::put('last_activity', time());

        return $next($request);
    }
}
