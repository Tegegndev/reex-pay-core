<?php

namespace App\Http\Middleware;

use App\Models\IPBlock;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class BlockIp
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // Get the list of blocked IPs from cache (24 hours or forever)
        $blockedIps = Cache::remember('blocked_ips_list', 86400, function () {
            return IPBlock::pluck('ip_address')->all();
        });

        // If the IP is in the blocked list, return a 403 response
        if (in_array($ip, $blockedIps)) {
            abort(403, 'Your IP is blocked.');
        }

        return $next($request);
    }
}
