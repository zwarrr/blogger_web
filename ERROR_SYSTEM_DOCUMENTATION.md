# Sistem Error Handling - Web Blogger

## Overview
Sistem error handling yang komprehensif untuk aplikasi web blogger Laravel, mencakup halaman error kustom, logging otomatis, dan handling berbagai jenis exception.

## Komponen Sistem

### 1. Halaman Error Kustom
Lokasi: `resources/views/error/`

#### File-file yang tersedia:
- **401.blade.php** - Unauthorized (butuh login)
- **403.blade.php** - Forbidden (akses ditolak)
- **404.blade.php** - Not Found (halaman tidak ditemukan)
- **419.blade.php** - Page Expired (CSRF token expired)
- **429.blade.php** - Too Many Requests (rate limiting)
- **500.blade.php** - Internal Server Error (kesalahan server)
- **503.blade.php** - Service Unavailable (maintenance mode)

#### Fitur Halaman Error:
- Desain responsif dengan Tailwind CSS
- Animasi partikel interaktif
- Pesan error dalam bahasa Indonesia
- Tombol navigasi yang sesuai konteks
- Efek visual glow yang berbeda untuk setiap error

### 2. Exception Handler (`app/Exceptions/Handler.php`)

#### Fitur:
- **Custom Error Views**: Otomatis menampilkan halaman error kustom
- **JSON Response**: Memberikan response JSON untuk AJAX requests
- **Error Logging**: Logging otomatis dengan konteks lengkap
- **Exception Routing**: Mengarahkan berbagai jenis exception ke halaman yang tepat

#### Exception yang di-handle:
```php
- AuthenticationException → 401
- AuthorizationException → 403  
- ModelNotFoundException → 404
- NotFoundHttpException → 404
- TokenMismatchException → 419
- ValidationException → 422
- HttpException → sesuai status code
- General Exception → 500
```

### 3. Error Log Service (`app/Services/ErrorLogService.php`)

#### Fitur:
- **Comprehensive Logging**: Log error dengan konteks request
- **HTTP Error Logging**: Khusus untuk HTTP errors
- **Smart Filtering**: Menentukan error mana yang perlu di-report
- **Structured Data**: Format log yang terstruktur untuk analisis

#### Method yang tersedia:
```php
ErrorLogService::logError($exception, $request, $additionalContext)
ErrorLogService::logHttpError($statusCode, $request, $message)
ErrorLogService::shouldReport($exception)
```

### 4. Custom Error Handler Middleware (`app/Http/Middleware/CustomErrorHandler.php`)

#### Fitur:
- **Response Interception**: Menangkap HTTP error responses
- **Conditional Rendering**: Hanya menampilkan error page untuk non-AJAX requests
- **Status Code Mapping**: Mapping status code ke pesan error yang sesuai

## Konfigurasi

### 1. Middleware Registration
Middleware telah didaftarkan di `app/Http/Kernel.php`:
```php
'web' => [
    // ... middleware lain
    \App\Http\Middleware\CustomErrorHandler::class,
],

'middlewareAliases' => [
    // ... alias lain
    'error.handler' => \App\Http\Middleware\CustomErrorHandler::class,
],
```

### 2. Environment Configuration
Pastikan pengaturan berikut di `.env`:
```env
APP_DEBUG=false  # untuk production
LOG_CHANNEL=stack
LOG_LEVEL=error
```

## Cara Penggunaan

### 1. Menambah Halaman Error Baru
Buat file baru di `resources/views/error/` dengan nama status code (misal: `400.blade.php`)

### 2. Custom Error dalam Controller
```php
// Throw specific HTTP exception
abort(404, 'Data tidak ditemukan');
abort(403, 'Akses ditolak');

// Return custom error response
return response()->view('error.custom', $data, 422);
```

### 3. Manual Error Logging
```php
use App\Services\ErrorLogService;

// Log custom error
ErrorLogService::logError($exception, request(), ['context' => 'value']);

// Log HTTP error
ErrorLogService::logHttpError(404, request(), 'Custom message');
```

## Testing Error Pages

### 1. Via Routes (untuk testing)
Tambahkan route testing di `routes/web.php`:
```php
Route::get('/test-error/{code}', function ($code) {
    abort($code);
})->name('test.error');
```

### 2. Via Artisan Command
```bash
# Test 404 error
php artisan route:list | grep "test-error"

# Test via browser
http://localhost/test-error/404
http://localhost/test-error/403
```

## Monitoring dan Logs

### 1. Log Files
- Error logs tersimpan di `storage/logs/laravel.log`
- Format log terstruktur dengan konteks lengkap

### 2. Log Analysis
```bash
# Lihat error terbaru
tail -f storage/logs/laravel.log

# Filter error berdasarkan status code
grep "HTTP Error 404" storage/logs/laravel.log

# Count error berdasarkan type
grep -c "AuthenticationException" storage/logs/laravel.log
```

## Customization

### 1. Mengubah Desain Error Page
Edit file di `resources/views/error/` sesuai kebutuhan desain.

### 2. Menambah Context Data
Update Handler.php untuk menambah data ke error views:
```php
return response()->view("error.{$statusCode}", [
    'exception' => $exception,
    'statusCode' => $statusCode,
    'customData' => $yourCustomData
], $statusCode);
```

### 3. Custom Error Messages
Update `ErrorLogService::getHttpErrorMessage()` untuk pesan kustom.

## Best Practices

### 1. Security
- Jangan tampilkan stack trace di production
- Sanitize error messages yang ditampilkan ke user
- Log detail error untuk debugging internal

### 2. User Experience
- Berikan pesan error yang jelas dalam bahasa Indonesia
- Sediakan navigasi kembali ke halaman utama
- Gunakan design yang konsisten dengan aplikasi

### 3. Performance
- Cache error views jika memungkinkan
- Minimize external dependencies pada error pages
- Optimasi asset loading untuk error pages

## Troubleshooting

### 1. Error Pages Tidak Muncul
- Pastikan `APP_DEBUG=false`
- Check middleware sudah terdaftar
- Verify file error view exists

### 2. Logs Tidak Tersimpan
- Check permission folder `storage/logs/`
- Verify log configuration di `config/logging.php`

### 3. Custom Handler Tidak Bekerja
- Clear cache: `php artisan config:clear`
- Check middleware order di Kernel.php
- Verify exception mapping di Handler.php