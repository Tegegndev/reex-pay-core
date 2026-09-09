<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DemoMode
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.demo')) {
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {

                notifyEvs('error', 'This action is disabled in demo mode.');

                return redirect()->back();
            }

            // Block "Login as User" route by route name
            if (
                $request->route() && $request->route()->getName() === 'admin.user.login'
            ) {
                notifyEvs('error', 'This action is disabled in demo mode.');

                return redirect()->back();
            }
        }

        return $next($request);
    }
}
