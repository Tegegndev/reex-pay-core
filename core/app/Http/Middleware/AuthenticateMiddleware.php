<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AuthenticateMiddleware extends Authenticate
{
    protected array $guards = [];

    /**
     * Handle an incoming request.
     *
     * This method is responsible for authenticating the user based on the provided guards.
     * It sets the guards property and then calls the parent handle method.
     *
     * @param  Request $request   The incoming request.
     * @param  Closure $next      The next middleware.
     * @param  string  ...$guards The guards to use for authentication.
     * @return mixed   The response from the parent handle method.
     *
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards): mixed
    {
        // Set the guards property
        $this->guards = $guards;

        // Call the parent handle method
        return parent::handle($request, $next, ...$guards);
    }

    /**
     * Redirect the user based on the authentication guard and request type.
     *
     * @param  Request     $request The incoming request.
     * @return string|void The route to redirect to.
     */
    protected function redirectTo(Request $request)
    {
        // Check if the request expects a JSON response
        if (! $request->expectsJson()) {
            // Get the first guard from the guards property
            $firstGuard = Arr::first($this->guards);

            // Check if the first guard is 'admin'
            if ($firstGuard === 'admin') {
                // Redirect to the admin login route
                return route('admin.login');
            }

            // Redirect to the login route
            return route('user.login');
        }
    }
}
