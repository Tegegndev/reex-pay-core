<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (HttpException $exception, $request) {
            return $this->handleHttpException($exception, $request);
        });

        $this->reportable(function (Throwable $exception) {
            //
        });
    }

    /**
     * Handle HTTP exceptions to ensure custom messages are displayed.
     */
    protected function handleHttpException(HttpException $exception, $request): JsonResponse
    {
        return response()->json([
            'message' => $exception->getMessage() ?: $this->getDefaultHttpErrorMessage($exception->getStatusCode()),
        ], $exception->getStatusCode());
    }

    /**
     * Get a default message for HTTP errors if no message is provided.
     */
    protected function getDefaultHttpErrorMessage(int $statusCode): string
    {
        $defaultMessages = [
            400 => 'Bad Request.',
            401 => 'Unauthorized access.',
            403 => 'Access forbidden.',
            404 => 'Resource not found.',
            405 => 'Method not allowed.',
            419 => 'Session expired.',
            429 => 'Too many requests, please slow down.',
            500 => 'Server error, please try again later.',
        ];

        return $defaultMessages[$statusCode] ?? 'An error occurred.';
    }
}
