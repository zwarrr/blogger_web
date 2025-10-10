# Perbaikan Storage Path untuk Foto dan Maps

## Perubahan yang Dilakukan

### 1. Perbaikan Path Storage untuk Foto Selfie

#### AdminVisitController.php - Method showJson():
```php
// BEFORE:
'selfie_photo' => $visit->selfie_photo,

// AFTER:
'selfie_photo' => $visit->selfie_photo ? asset('storage/visits/selfies/' . basename($visit->selfie_photo)) : null,
```

**Hasil:**
- Foto selfie sekarang dibaca dari `storage/app/public/visits/selfies/`
- URL yang dihasilkan: `http://domain.com/storage/visits/selfies/filename.jpg`
- Menggunakan `basename()` untuk mengambil nama file saja

### 2. Perbaikan Path Storage untuk Foto Dokumentasi

#### AdminVisitController.php - Method showJson():
```php
// Process photos to use correct storage paths
$photos = [];
if (is_array($visit->photos)) {
    foreach ($visit->photos as $photo) {
        $photos[] = asset('storage/visits/photos/' . basename($photo));
    }
}

// Then use in report data:
'photos' => $photos,
```

**Hasil:**
- Foto dokumentasi sekarang dibaca dari `storage/app/public/visits/photos/`
- Setiap foto diproses untuk menggunakan URL yang benar
- URL yang dihasilkan: `http://domain.com/storage/visits/photos/filename.jpg`

### 3. Struktur Folder Storage yang Dibuat

```
storage/app/public/visits/
├── selfies/     # Untuk foto selfie auditor
└── photos/      # Untuk foto dokumentasi kunjungan
```

### 4. Peningkatan Tampilan Maps

#### Lokasi Kunjungan:
- **Background styling** dengan `bg-gray-50 p-3 rounded-lg`
- **Icon lokasi** untuk alamat
- **Koordinat** ditampilkan dengan jelas
- **Dual maps support**: Google Maps + Waze
- **Button styling** yang lebih menarik

#### Lokasi Selfie:
- **Koordinat selfie** ditampilkan terpisah
- **Button Google Maps** khusus untuk lokasi selfie
- **Enhanced styling** untuk foto selfie

### 5. Peningkatan UX untuk Foto

#### Foto Selfie:
```javascript
// Hover effects dan cursor pointer
class="cursor-pointer hover:opacity-80 transition-opacity"
style="cursor: pointer;"
```

#### Foto Dokumentasi:
```javascript
// Counter foto dan tooltip
title="Klik untuk memperbesar"
'Foto Dokumentasi (' + visit.report.photos.length + ' foto)'
```

## Fitur Maps yang Ditingkatkan

### 1. Lokasi Kunjungan
- ✅ **Google Maps**: Link langsung ke koordinat
- ✅ **Waze Navigation**: Link navigasi untuk mobile
- ✅ **Styled buttons** dengan icon dan warna berbeda
- ✅ **Responsive layout** dengan flex dan gap

### 2. Lokasi Selfie
- ✅ **Koordinat selfie** terpisah dari koordinat kunjungan
- ✅ **Direct Google Maps** link ke lokasi foto
- ✅ **Visual differentiation** antara lokasi kunjungan dan selfie

## Contoh URL yang Dihasilkan

### Foto Selfie:
```
http://127.0.0.1:8000/storage/visits/selfies/selfie_20241009_123456.jpg
```

### Foto Dokumentasi:
```
http://127.0.0.1:8000/storage/visits/photos/doc_20241009_123456.jpg
```

### Maps Links:
```
Google Maps: https://maps.google.com/maps?q=-6.208763,106.845599
Waze: https://waze.com/ul?ll=-6.208763,106.845599&navigate=yes
```

## Testing

### 1. Test Storage Link
```bash
php artisan storage:link
```

### 2. Test Path Creation
- Folder `storage/app/public/visits/selfies/` ✅ Created
- Folder `storage/app/public/visits/photos/` ✅ Created

### 3. Test Modal Detail
1. Login sebagai admin
2. Akses `/admin/visits`
3. Klik "Lihat Detail" pada kunjungan yang memiliki foto
4. Verifikasi:
   - Foto selfie muncul dengan URL storage yang benar
   - Koordinat selfie ditampilkan
   - Link Google Maps berfungsi
   - Foto dokumentasi muncul dengan path yang benar
   - Button maps memiliki styling yang menarik

## Error Handling

### Foto tidak ditemukan:
- Menggunakan `basename()` untuk keamanan path
- Null check dengan `? asset(...) : null`
- Graceful fallback jika foto tidak ada

### Koordinat tidak valid:
- Check existence: `if (visit.latitude && visit.longitude)`
- Proper URL encoding untuk maps links

## Struktur Data yang Diharapkan

### Database visits table:
```sql
selfie_photo: 'filename.jpg' (nama file saja)
selfie_latitude: -6.208763
selfie_longitude: 106.845599
photos: '["photo1.jpg","photo2.jpg"]' (JSON array nama file)
latitude: -6.208763 (koordinat lokasi kunjungan)
longitude: 106.845599
```

### JSON Response:
```json
{
  "report": {
    "selfie_photo": "http://domain.com/storage/visits/selfies/filename.jpg",
    "selfie_latitude": -6.208763,
    "selfie_longitude": 106.845599,
    "photos": [
      "http://domain.com/storage/visits/photos/photo1.jpg",
      "http://domain.com/storage/visits/photos/photo2.jpg"
    ]
  },
  "latitude": -6.208763,
  "longitude": 106.845599
}
```

## Catatan Penting
- **Storage link** harus sudah dibuat: `php artisan storage:link`
- **File permissions** harus benar untuk folder storage
- **Nama file** di database sebaiknya hanya nama file, bukan full path
- **Koordinat** harus dalam format desimal (float), bukan string