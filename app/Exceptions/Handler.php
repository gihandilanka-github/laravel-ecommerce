<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        OrderValidationException::class,
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Add any custom reporting logic here
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });

        // Convert all exceptions to JSON for API requests
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                return $this->handleApiException($e);
            }
        });
    }

    private function handleApiException(Throwable $exception): JsonResponse
    {
        return match (true) {
            $exception instanceof ValidationException => $this->handleValidationException($exception),
            $exception instanceof ModelNotFoundException,
            $exception instanceof NotFoundHttpException => $this->handleNotFoundException($exception),
            $exception instanceof AuthenticationException => $this->handleAuthenticationException($exception),
            $exception instanceof TooManyRequestsHttpException => $this->handleRateLimitException($exception),
            $exception instanceof OrderException => $this->handleOrderException($exception),
            default => $this->handleDefaultException($exception),
        };
    }

    private function handleValidationException(ValidationException $exception): JsonResponse
    {
        return response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $exception->errors(),
        ], 422);
    }

    private function handleNotFoundException(Throwable $exception): JsonResponse
    {
        return response()->json([
            'message' => 'Resource not found.',
            'error' => 'NOT_FOUND',
        ], 404);
    }

    private function handleAuthenticationException(AuthenticationException $exception): JsonResponse
    {
        return response()->json([
            'message' => 'Unauthenticated.',
            'error' => 'UNAUTHENTICATED',
        ], 401);
    }

    private function handleRateLimitException(TooManyRequestsHttpException $exception): JsonResponse
    {
        return response()->json([
            'message' => 'Too many requests.',
            'error' => 'RATE_LIMIT_EXCEEDED',
        ], 429);
    }

    private function handleOrderException(OrderException $exception): JsonResponse
    {
        return response()->json([
            'message' => $exception->getMessage(),
            'error' => $exception->getErrorCode(),
            'details' => $exception->getDetails(),
        ], $exception->getStatusCode());
    }

    private function handleDefaultException(Throwable $exception): JsonResponse
    {
        return response()->json([
            'message' => app()->environment('production')
                ? 'An unexpected error occurred.'
                : $exception->getMessage(),
            'error' => 'INTERNAL_SERVER_ERROR',
        ], 500);
    }
}
