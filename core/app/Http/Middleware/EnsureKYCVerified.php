<?php

namespace App\Http\Middleware;

use App\Exceptions\NotifyErrorException;
use Closure;
use Illuminate\Http\Request;

class EnsureKYCVerified
{
    /**
     * @throws NotifyErrorException
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || ! $user->isKycVerified()) {
            throw new NotifyErrorException('KYC verification required to perform this action.', 403);
        }

        return $next($request);
    }
}
