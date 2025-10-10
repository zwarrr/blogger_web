# Perbaikan Masalah Admin Visits Routing

## Masalah yang Ditemukan
1. **Route Conflict**: Route `/visits/{visit}` menangkap "create" sebagai parameter
2. **Authentication Required**: Halaman admin memerlukan login sebagai admin

## Solusi yang Diterapkan

### 1. Perbaikan Route Order dan Constraints
```php
// routes/web.php - Urutan route yang diperbaiki:

// Specific routes first (non-parameterized)
Route::get('/visits', [AdminVisitController::class, 'index'])->name('admin.visits.index');
Route::get('/visits/create', [AdminVisitController::class, 'create'])->name('admin.visits.create');

// Parameterized routes with regex constraints
Route::get('/visits/{visit}/json', [AdminVisitController::class, 'showJson'])
    ->name('admin.visits.show.json')
    ->where('visit', '[0-9]+');

Route::get('/visits/{visit}/edit', [AdminVisitController::class, 'edit'])
    ->name('admin.visits.edit')
    ->where('visit', '[0-9]+');

Route::get('/visits/{visit}', [AdminVisitController::class, 'show'])
    ->name('admin.visits.show')
    ->where('visit', '[0-9]+');
```

### 2. Regex Constraints Ditambahkan
- `->where('visit', '[0-9]+')` memastikan parameter {visit} hanya menerima angka
- Ini mencegah "create" dianggap sebagai ID visit

## Cara Mengakses Halaman Admin Visits

### 1. Login sebagai Admin terlebih dahulu
```
URL: http://127.0.0.1:8000/admin/login
```

### 2. Setelah login, akses halaman visits:
```
URL: http://127.0.0.1:8000/admin/visits
```

## Testing Route

### 1. Clear cache setelah perubahan:
```bash
php artisan route:clear
php artisan config:clear  
php artisan cache:clear
```

### 2. Verifikasi route list:
```bash
php artisan route:list | findstr visits
```

### 3. Expected Routes:
```
GET    admin/visits .................. admin.visits.index
GET    admin/visits/create .......... admin.visits.create  
GET    admin/visits/{visit} ......... admin.visits.show
GET    admin/visits/{visit}/json .... admin.visits.show.json
GET    admin/visits/{visit}/edit .... admin.visits.edit
```

## Troubleshooting

### Jika masih redirect ke create:
1. Pastikan sudah login sebagai admin
2. Clear browser cache
3. Periksa URL yang diakses: `/admin/visits` (bukan `/admin/visits/create`)

### Jika error 404:
1. Pastikan server Laravel berjalan: `php artisan serve`
2. Periksa route dengan: `php artisan route:list`

### Jika masih ada masalah:
1. Periksa log Laravel: `storage/logs/laravel.log`
2. Enable debug mode di `.env`: `APP_DEBUG=true`
3. Check middleware admin di `app/Http/Middleware/`

## URLs yang Benar

### Admin Visits Management:
- **List Visits**: `/admin/visits`
- **Create Visit**: `/admin/visits/create` 
- **Show Visit**: `/admin/visits/{id}` (contoh: `/admin/visits/1`)
- **Edit Visit**: `/admin/visits/{id}/edit` (contoh: `/admin/visits/1/edit`)
- **Visit Detail JSON**: `/admin/visits/{id}/json` (contoh: `/admin/visits/1/json`)

### Authentication:
- **Admin Login**: `/admin/login`
- **Admin Dashboard**: `/admin/dashboard`

## Catatan Penting
- Semua route admin memerlukan authentication dengan role 'admin'
- Parameter {visit} sekarang hanya menerima ID numerik (regex: [0-9]+)
- Route specific (`/create`) didahulukan sebelum route parameterized (`/{visit}`)