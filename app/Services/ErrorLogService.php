<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Throwable;

class ErrorLogService
{
    /**
     * Log error with additional context
     */
    public static function logError(Throwable $exception, Request $request = null, array $additionalContext = []): void
    {
        $context = [
            'exception' => [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ],
            'request' => $request ? [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => $request->user()?->id,
                'session_id' => $request->session()?->getId(),
                'input' => $request->except(['password', 'password_confirmation', '_token']),
            ] : null,
            'timestamp' => now()->toISOString(),
            'additional' => $additionalContext,
        ];

        Log::error('Application Error: ' . $exception->getMessage(), $context);
    }

    /**
     * Log HTTP error
     */
    public static function logHttpError(int $statusCode, Request $request = null, string $message = null): void
    {
        $context = [
            'status_code' => $statusCode,
            'message' => $message ?? self::getHttpErrorMessage($statusCode),
            'request' => $request ? [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => $request->user()?->id,
                'referer' => $request->header('referer'),
            ] : null,
            'timestamp' => now()->toISOString(),
        ];

        $logLevel = $statusCode >= 500 ? 'error' : 'warning';
        Log::$logLevel("HTTP Error {$statusCode}: " . ($message ?? self::getHttpErrorMessage($statusCode)), $context);
    }

    /**
     * Get standard HTTP error message
     */
    private static function getHttpErrorMessage(int $statusCode): string
    {
        return match($statusCode) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            419 => 'Page Expired',
            422 => 'Unprocessable Entity',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            default => 'HTTP Error'
        };
    }

    /**
     * Check if error should be reported
     */
    public static function shouldReport(Throwable $exception): bool
    {
        // Don't report certain types of exceptions
        $dontReport = [
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            \Illuminate\Database\Eloquent\ModelNotFoundException::class,
            \Illuminate\Session\TokenMismatchException::class,
            \Illuminate\Validation\ValidationException::class,
            \Symfony\Component\HttpKernel\Exception\HttpException::class,
        ];

        foreach ($dontReport as $type) {
            if ($exception instanceof $type) {
                return false;
            }
        }

        return true;
    }
}