# 🚨 Error Handling System - Quick Guide

## 📋 Ringkasan Sistem
Sistem error handling yang telah diimplementasi mencakup:

### ✅ Halaman Error Kustom
- **401** - Unauthorized (Perlu Login) - 🔐 Pink theme
- **403** - Forbidden (Akses Ditolak) - 🔴 Red theme  
- **404** - Not Found (Halaman Tidak Ditemukan) - 🟠 Orange theme
- **419** - Page Expired (CSRF Token) - 🔵 Indigo theme
- **429** - Too Many Requests (Rate Limit) - 🟣 Purple theme
- **500** - Internal Server Error - 🟤 Brown theme
- **503** - Service Unavailable (Maintenance) - 🟢 Green theme

### 🛠️ Komponen Sistem
1. **Exception Handler** (`app/Exceptions/Handler.php`)
2. **Error Middleware** (`app/Http/Middleware/CustomErrorHandler.php`)  
3. **Error Log Service** (`app/Services/ErrorLogService.php`)
4. **Error Views** (`resources/views/error/`)

## 🚀 Testing

### Command Line Testing
```bash
# Check all error pages
php artisan error:test --check

# Full test with URLs
php artisan error:test
```

### Browser Testing (Development Only)
```
http://localhost/test-errors          # Overview semua error
http://localhost/test-error/404       # Test error 404
http://localhost/test-error/500       # Test error 500
```

## ⚡ Quick Usage

### Trigger Error di Controller
```php
// Throw specific error
abort(404, 'Data tidak ditemukan');
abort(403, 'Akses ditolak');
abort(500, 'Kesalahan server');

// Return custom error view
return response()->view('error.404', $data, 404);
```

### Manual Error Logging
```php
use App\Services\ErrorLogService;

// Log error dengan context
ErrorLogService::logError($exception, request());

// Log HTTP error
ErrorLogService::logHttpError(404, request(), 'Custom message');
```

## 🎨 Fitur Halaman Error
- ✨ Animasi partikel interaktif
- 📱 Responsive design dengan Tailwind CSS
- 🇮🇩 Pesan dalam bahasa Indonesia
- 🎯 Navigasi kontekstual (login, home, refresh)
- 🌈 Color theme berbeda untuk setiap error
- ⚡ Loading yang cepat dengan minimal dependencies

## 📁 File Structure
```
app/
├── Console/Commands/TestErrorSystem.php
├── Exceptions/Handler.php
├── Http/Middleware/CustomErrorHandler.php
└── Services/ErrorLogService.php

resources/views/
├── error/
│   ├── 401.blade.php
│   ├── 403.blade.php
│   ├── 404.blade.php
│   ├── 419.blade.php
│   ├── 429.blade.php
│   ├── 500.blade.php
│   └── 503.blade.php
└── test-errors.blade.php
```

## 🔧 Configuration

Middleware sudah terdaftar di `app/Http/Kernel.php`:
```php
'web' => [
    // ...
    \App\Http\Middleware\CustomErrorHandler::class,
],
```

## 📊 Monitoring

### Log Analysis
```bash
# Lihat error logs
tail -f storage/logs/laravel.log

# Filter by error type
grep "HTTP Error 404" storage/logs/laravel.log

# Count errors
grep -c "AuthenticationException" storage/logs/laravel.log
```

## 🔒 Security Notes
- Stack traces tidak ditampilkan di production
- Error testing routes hanya aktif di environment `local` dan `testing`
- Sensitive data di-filter dari error logs
- Custom error messages untuk user safety

## 📈 Benefits
- ✅ **User Experience**: Error pages yang profesional dan informatif
- ✅ **Developer Experience**: Logging otomatis dengan konteks lengkap  
- ✅ **SEO Friendly**: Proper HTTP status codes
- ✅ **Security**: Error details tidak bocor ke public
- ✅ **Monitoring**: Structured logging untuk analisis
- ✅ **Maintenance**: Mudah dikustomisasi dan di-extend

---

💡 **Tip**: Jalankan `php artisan error:test` secara berkala untuk memastikan semua komponen error handling berfungsi dengan baik!