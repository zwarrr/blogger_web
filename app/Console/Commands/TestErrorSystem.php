<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class TestErrorSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'error:test {--check : Only check if error pages exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the error handling system and verify error pages exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testing Error Handling System...');
        $this->newLine();

        $errorCodes = [401, 403, 404, 419, 429, 500, 503];
        $results = [];

        foreach ($errorCodes as $code) {
            $results[$code] = $this->checkErrorPage($code);
        }

        $this->displayResults($results);

        if ($this->option('check')) {
            return;
        }

        $this->newLine();
        $this->info('🌐 Testing URLs (if in local environment):');
        
        if (app()->environment('local')) {
            $this->info('   • Test all errors: http://localhost/test-errors');
            $this->info('   • Test specific error: http://localhost/test-error/{code}');
        } else {
            $this->warn('   URL testing only available in local environment');
        }

        $this->newLine();
        $this->info('✅ Error system check completed!');
    }

    private function checkErrorPage(int $code): array
    {
        $viewPath = "error.{$code}";
        $filePath = resource_path("views/error/{$code}.blade.php");
        
        return [
            'exists' => File::exists($filePath),
            'view_exists' => View::exists($viewPath),
            'file_path' => $filePath,
            'size' => File::exists($filePath) ? File::size($filePath) : 0
        ];
    }

    private function displayResults(array $results): void
    {
        $this->info('📄 Error Pages Status:');
        $this->newLine();

        $headers = ['Code', 'Status', 'File Size', 'Path'];
        $rows = [];

        foreach ($results as $code => $result) {
            $status = $result['exists'] ? '✅ EXISTS' : '❌ MISSING';
            $size = $result['size'] > 0 ? $this->formatBytes($result['size']) : '-';
            
            $rows[] = [
                $code,
                $status,
                $size,
                $result['exists'] ? '✓ Found' : '✗ Not Found'
            ];
        }

        $this->table($headers, $rows);

        // Summary
        $existing = collect($results)->where('exists', true)->count();
        $total = count($results);
        
        $this->newLine();
        if ($existing === $total) {
            $this->info("🎉 All {$total} error pages are ready!");
        } else {
            $this->warn("⚠️  {$existing}/{$total} error pages exist. Missing: " . 
                collect($results)->filter(fn($r) => !$r['exists'])->keys()->implode(', '));
        }

        // Check Handler and Middleware
        $this->newLine();
        $this->info('🔧 System Components:');
        
        $handlerPath = app_path('Exceptions/Handler.php');
        $middlewarePath = app_path('Http/Middleware/CustomErrorHandler.php');
        $servicePath = app_path('Services/ErrorLogService.php');
        
        $components = [
            ['Exception Handler', File::exists($handlerPath) ? '✅' : '❌', $handlerPath],
            ['Error Middleware', File::exists($middlewarePath) ? '✅' : '❌', $middlewarePath],
            ['Log Service', File::exists($servicePath) ? '✅' : '❌', $servicePath],
        ];
        
        $this->table(['Component', 'Status', 'Path'], $components);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
