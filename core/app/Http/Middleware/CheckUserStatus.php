<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if the user exists and is not active
        if ($user && $user->status !== UserStatus::ACTIVE) {
            // Optionally, you can check for other statuses, e.g., SUSPENDED or INACTIVE
            auth()->logout();

            return redirect()->route('user.login')
                ->withErrors(['error' => __('Your account is not active. Please contact support.')]);
        }

        return $next($request);
    }
}
