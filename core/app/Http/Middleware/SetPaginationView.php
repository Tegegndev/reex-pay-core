<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Pagination\Paginator;

class SetPaginationView
{
    public function handle($request, Closure $next)
    {
        // Set default pagination for admin dashboard
        if ($request->routeIs('admin.*')) {
            Paginator::defaultView('backend.pagination.default');
        } else {
            Paginator::defaultView('frontend.pagination.default');
        }

        return $next($request);
    }
}
