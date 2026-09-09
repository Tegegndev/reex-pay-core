<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XSS
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only sanitize on methods that receive user input
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $sanitized = $this->cleanInput($request->all());
            $request->merge($sanitized);
        }

        return $next($request);
    }

    /**
     * Recursively clean the input array using strip_tags.
     */
    protected function cleanInput(array $input): array
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $input[$key] = $this->cleanInput($value);
            } elseif (is_string($value)) {
                $input[$key] = strip_tags($value);
            }
        }

        return $input;
    }
}
