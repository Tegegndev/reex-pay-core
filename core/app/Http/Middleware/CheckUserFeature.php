<?php

namespace App\Http\Middleware;

use App\Exceptions\NotifyErrorException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserFeature
{
    /**
     * @throws NotifyErrorException
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if (auth()->user() && ! auth()->user()->hasFeature($feature)) {
            throw new NotifyErrorException(__('You do not have permission to access this feature.'), 403);
        }

        return $next($request);
    }
}
