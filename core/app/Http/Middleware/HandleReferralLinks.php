<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class HandleReferralLinks
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('ref')) {
            // Store the referral code in a cookie (7 days expiration)
            Cookie::queue('referral_code', $request->query('ref'), 60 * 24 * 7);
        }

        return $next($request);
    }
}
