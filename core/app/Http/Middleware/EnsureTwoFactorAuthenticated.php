<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTwoFactorAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * Checks for authenticated admin or user and redirects
     * to the appropriate 2FA verification page if 2FA is enabled
     * but not yet verified in the session.
     *
     * @param  Closure(Request): (\Illuminate\Http\Response|RedirectResponse) $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if an admin is authenticated
        if (Auth::guard('admin')->check()) {
            $user          = Auth::guard('admin')->user();
            $redirectRoute = route('admin.two-factor-challenge');
        }
        // Otherwise check if a regular user is authenticated
        elseif (Auth::check()) {
            $user          = Auth::user();
            $redirectRoute = route('user.two-factor-challenge');
        } else {
            // If no authenticated user, redirect to login page
            return redirect()->route('user.login');
        }

        // If 2FA is enabled but not yet verified in session, redirect
        if ($user->two_factor_enabled && ! $request->session()->get('2fa_verified')) {
            return redirect($redirectRoute);
        }

        return $next($request);
    }
}
