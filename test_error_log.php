<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Log;

// Set environment untuk testing
$_ENV['APP_ENV'] = 'local';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Error Log System...\n";

// Test different types of errors
echo "1. Testing ERROR level...\n";
Log::error('Test ERROR: Database connection failed', [
    'connection' => 'mysql',
    'host' => 'localhost',
    'error_code' => 2002
]);

echo "2. Testing CRITICAL level...\n";
Log::critical('Test CRITICAL: Payment gateway down', [
    'gateway' => 'stripe',
    'transaction_id' => 'tx_12345',
    'amount' => 100000
]);

echo "3. Testing WARNING level...\n";
Log::warning('Test WARNING: High memory usage detected', [
    'memory_usage' => '85%',
    'threshold' => '80%'
]);

echo "4. Testing EMERGENCY level...\n";
Log::emergency('Test EMERGENCY: System under attack', [
    'ip_address' => '192.168.1.100',
    'attack_type' => 'brute_force',
    'attempts' => 1000
]);

echo "5. Creating error with exception...\n";
try {
    throw new Exception('Test exception for log parsing');
} catch (Exception $e) {
    Log::error('Test ERROR with Exception: ' . $e->getMessage(), [
        'exception' => $e,
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
}

echo "6. Testing SQL error simulation...\n";
Log::error('Test SQL ERROR: SQLSTATE[42S02]: Base table or view not found: 1146 Table \'blogger.non_existent_table\' doesn\'t exist', [
    'query' => 'SELECT * FROM non_existent_table',
    'bindings' => []
]);

echo "\nError log testing completed!\n";
echo "Check your admin logs page to see the generated errors.\n";
echo "URL: http://your-domain/admin/logs/error\n";