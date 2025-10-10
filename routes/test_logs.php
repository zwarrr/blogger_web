<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

Route::get('/test-error-logs', function () {
    try {
        Log::info('Starting error log test...');
        
        // Test different types of errors
        Log::error('Test ERROR: Database connection failed', [
            'connection' => 'mysql',
            'host' => 'localhost',
            'error_code' => 2002,
            'file' => __FILE__,
            'line' => __LINE__
        ]);

        Log::critical('Test CRITICAL: Payment gateway down', [
            'gateway' => 'stripe',
            'transaction_id' => 'tx_12345',
            'amount' => 100000,
            'file' => __FILE__,
            'line' => __LINE__
        ]);

        Log::warning('Test WARNING: High memory usage detected', [
            'memory_usage' => '85%',
            'threshold' => '80%',
            'file' => __FILE__,
            'line' => __LINE__
        ]);

        Log::emergency('Test EMERGENCY: System under attack', [
            'ip_address' => '192.168.1.100',
            'attack_type' => 'brute_force',
            'attempts' => 1000,
            'file' => __FILE__,
            'line' => __LINE__
        ]);

        // Creating error with exception
        try {
            throw new Exception('Test exception for log parsing with full stack trace');
        } catch (Exception $e) {
            Log::error('Test ERROR with Exception: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        // Test SQL error simulation
        Log::error('Test SQL ERROR: SQLSTATE[42S02]: Base table or view not found: 1146 Table \'blogger.non_existent_table\' doesn\'t exist', [
            'query' => 'SELECT * FROM non_existent_table WHERE id = ?',
            'bindings' => [1],
            'file' => __FILE__,
            'line' => __LINE__
        ]);

        // Test PHP Fatal Error simulation
        Log::error('Test PHP Fatal Error: Uncaught Error: Call to undefined function non_existent_function()', [
            'type' => 'Fatal Error',
            'file' => '/path/to/problematic/file.php',
            'line' => 123,
            'trace' => "Stack trace:\n#0 /path/to/file.php(123): non_existent_function()\n#1 {main}"
        ]);

        Log::info('Error log test completed successfully');

        return response()->json([
            'success' => true,
            'message' => 'Test error logs berhasil dibuat! Silakan cek halaman admin logs.',
            'logs_generated' => 6,
            'redirect_url' => route('admin.logs.error')
        ]);

    } catch (Exception $e) {
        Log::error('Failed to generate test logs: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal membuat test logs: ' . $e->getMessage()
        ], 500);
    }
});