<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\ErrorLogService;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
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
        $this->reportable(function (Throwable $e) {
            // Use custom error logging service
            if (ErrorLogService::shouldReport($e)) {
                ErrorLogService::logError($e, request());
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Handle specific exceptions with custom error pages
        if ($this->isHttpException($exception)) {
            /** @var \Symfony\Component\HttpKernel\Exception\HttpException $exception */
            $statusCode = $exception->getStatusCode();
            
            // Log HTTP error
            ErrorLogService::logHttpError($statusCode, $request, $exception->getMessage());
            
            // Check if custom error view exists
            if (view()->exists("error.{$statusCode}")) {
                return response()->view("error.{$statusCode}", [
                    'exception' => $exception,
                    'statusCode' => $statusCode
                ], $statusCode);
            }
        }

        // Handle authentication exceptions (401)
        if ($exception instanceof AuthenticationException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return response()->view('error.401', [
                'exception' => $exception,
                'statusCode' => 401
            ], 401);
        }

        // Handle authorization exceptions (403)
        if ($exception instanceof AuthorizationException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden.'], 403);
            }
            return response()->view('error.403', [
                'exception' => $exception,
                'statusCode' => 403
            ], 403);
        }

        // Handle model not found exceptions (404)
        if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Not Found.'], 404);
            }
            return response()->view('error.404', [
                'exception' => $exception,
                'statusCode' => 404
            ], 404);
        }

        // Handle CSRF token mismatch (419)
        if ($exception instanceof TokenMismatchException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Page Expired.'], 419);
            }
            return response()->view('error.419', [
                'exception' => $exception,
                'statusCode' => 419
            ], 419);
        }

        // Handle validation exceptions (422)
        if ($exception instanceof ValidationException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $exception->errors(),
                ], 422);
            }
        }

        // Handle server errors (500)
        if ($exception instanceof \Exception && !$this->isHttpException($exception)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Server Error.'], 500);
            }
            
            // Only show detailed error in debug mode
            if (config('app.debug')) {
                return parent::render($request, $exception);
            }
            
            return response()->view('error.500', [
                'exception' => $exception,
                'statusCode' => 500
            ], 500);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->view('error.401', [
            'exception' => $exception,
            'statusCode' => 401
        ], 401);
    }
}
