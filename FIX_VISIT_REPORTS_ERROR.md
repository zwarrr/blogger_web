# Fix: Error Table 'blogger_db.visit_reports' doesn't exist

## Masalah
Error: `SQLSTATE[42S02]: Base table or view not found: 1146 Table 'blogger_db.visit_reports' doesn't exist`

## Penyebab
- Controller mencoba mengakses relasi `visitReport` dengan eager loading `with(['visitReport'])`
- Tabel `visit_reports` belum dibuat dengan benar atau ada masalah dalam migrasi

## Solusi yang Diterapkan

### 1. Perbaikan AdminVisitController.php

#### Method index():
```php
// BEFORE (bermasalah):
$visits = $query->with(['author', 'auditor', 'visitReport'])
               ->orderBy('visit_date', 'desc')
               ->paginate(15);

// AFTER (diperbaiki):
$visits = $query->with(['author', 'auditor'])
               ->orderBy('visit_date', 'desc')
               ->paginate(15);
```

#### Method showJson():
```php
// BEFORE (bermasalah):
$visit->load(['author', 'auditor', 'visitReport']);
// Plus logic untuk mengakses $visit->visitReport

// AFTER (diperbaiki):
$visit->load(['author', 'auditor']);
// Menggunakan data langsung dari tabel visits
```

### 2. Perbaikan Model Visit.php

```php
// BEFORE (bermasalah):
public function visitReport()
{
    return $this->hasOne(VisitReport::class, 'visit_id', 'id');
}

// AFTER (sementara dicomment):
// Temporarily commented out until visit_reports table is created properly
// public function visitReport()
// {
//     return $this->hasOne(VisitReport::class, 'visit_id', 'id');
// }
```

### 3. Menggunakan Data dari Tabel visits Langsung

Alih-alih mengakses tabel terpisah `visit_reports`, sekarang sistem menggunakan kolom yang sudah ada di tabel `visits`:
- `report_notes` 
- `auditor_notes`
- `photos` (JSON array)
- `selfie_photo`
- `selfie_latitude` 
- `selfie_longitude`
- `started_at` (sebagai visit_start_time)
- `completed_at` (sebagai visit_end_time)

## Testing

### 1. Clear Cache
```bash
php artisan cache:clear
php artisan config:clear  
php artisan view:clear
```

### 2. Start Server
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

### 3. Test URL
- Login sebagai admin di: `http://127.0.0.1:8000/admin/login`
- Akses halaman visits: `http://127.0.0.1:8000/admin/visits`

## Fitur yang Tetap Berfungsi

✅ **Daftar Kunjungan**: Dapat melihat semua kunjungan
✅ **Filter dan Pencarian**: Semua filter berfungsi normal  
✅ **Detail Modal**: Modal detail menampilkan informasi lengkap
✅ **ACC/Reject**: Fungsi approve dan reject tetap bekerja
✅ **Action Menu**: Menu berdasarkan status (selesai hanya show detail)
✅ **Author/Auditor Data**: Nama author dan auditor tampil dengan benar

## Catatan Penting

### Struktur Data yang Digunakan:
Semua data laporan kunjungan tersimpan di tabel `visits` dengan kolom:
- `report_notes`: Catatan laporan auditor
- `auditor_notes`: Catatan tambahan auditor  
- `photos`: Array JSON foto dokumentasi
- `selfie_photo`: URL foto selfie
- `selfie_latitude`, `selfie_longitude`: Koordinat foto selfie
- `started_at`, `completed_at`: Waktu mulai dan selesai kunjungan

### Jika Ingin Menggunakan Tabel visit_reports Terpisah:
1. Pastikan migrasi `visit_reports` berhasil dijalankan
2. Uncomment relasi `visitReport()` di model Visit
3. Kembalikan eager loading `->with(['visitReport'])` di controller
4. Update logic dalam `showJson()` untuk menggunakan data dari `visit_reports`

## Commands untuk Rollback (Jika Diperlukan)

```bash
# Check status migrasi
php artisan migrate:status | findstr visit

# Rollback migrasi bermasalah (jika ada)
php artisan migrate:rollback --step=1

# Re-run migrasi
php artisan migrate
```

## URL Testing
- **Admin Login**: `http://127.0.0.1:8000/admin/login`
- **Visits List**: `http://127.0.0.1:8000/admin/visits`  
- **Create Visit**: `http://127.0.0.1:8000/admin/visits/create`
- **Visit Detail**: `http://127.0.0.1:8000/admin/visits/{id}`
- **Visit JSON**: `http://127.0.0.1:8000/admin/visits/{id}/json`