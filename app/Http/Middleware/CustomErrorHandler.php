<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomErrorHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Handle HTTP error responses
        if ($response instanceof Response) {
            $statusCode = $response->getStatusCode();
            
            // Check if it's an error status code and if we have a custom view
            if ($statusCode >= 400 && view()->exists("error.{$statusCode}")) {
                // Only show custom error page for non-AJAX requests
                if (!$request->expectsJson() && !$request->ajax()) {
                    return response()->view("error.{$statusCode}", [
                        'statusCode' => $statusCode,
                        'message' => $this->getErrorMessage($statusCode)
                    ], $statusCode);
                }
            }
        }

        return $response;
    }

    /**
     * Get error message for status code
     */
    private function getErrorMessage(int $statusCode): string
    {
        return match($statusCode) {
            400 => 'Permintaan tidak valid',
            401 => 'Anda perlu login terlebih dahulu',
            403 => 'Akses ditolak',
            404 => 'Halaman tidak ditemukan',
            405 => 'Method tidak diizinkan',
            419 => 'Halaman sudah kadaluarsa',
            422 => 'Data tidak valid',
            429 => 'Terlalu banyak permintaan',
            500 => 'Kesalahan server internal',
            502 => 'Bad Gateway',
            503 => 'Layanan tidak tersedia',
            504 => 'Gateway Timeout',
            default => 'Terjadi kesalahan'
        };
    }
}