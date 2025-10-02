# Kategori Blogger - Database Setup

## Struktur Database Telah Dibuat ✅

### Tabel Categories
- `id`: Primary key
- `name`: Nama kategori
- `slug`: URL-friendly name
- `icon`: Emoji icon
- `color`: Hex color code
- `description`: Deskripsi kategori
- `timestamps`: created_at, updated_at

### Tabel Posts (Updated)
- Ditambahkan kolom `category_id` (foreign key ke categories)

## Kategori yang Tersedia (5 Kategori Utama) ✅

### 📚 Tutorial & Tips
**Icon:** 📚 | **Color:** #3B82F6 (Blue)
- Panduan, tutorial, tips dan trik untuk memudahkan kehidupan sehari-hari
- Cocok untuk: How-to guides, step-by-step tutorials, life hacks

### ⭐ Review & Opini
**Icon:** ⭐ | **Color:** #F59E0B (Amber)
- Ulasan produk, opini, dan analisis mendalam
- Cocok untuk: Product reviews, analysis, personal opinions, critique

### 💻 Teknologi & Aplikasi
**Icon:** 💻 | **Color:** #8B5CF6 (Purple)
- Berita teknologi, gadget, aplikasi, dan perkembangan digital
- Cocok untuk: Tech news, gadget reviews, app updates, digital trends

### 🎓 Edukasi & Informasi
**Icon:** 🎓 | **Color:** #10B981 (Green)
- Artikel edukatif, informasi umum, dan pengetahuan
- Cocok untuk: Educational content, informative articles, knowledge sharing

### 🌿 Alam & Lingkungan
**Icon:** 🌿 | **Color:** #059669 (Emerald)
- Berita alam, lingkungan hidup, dan keberlanjutan
- Cocok untuk: Nature, environmental issues, sustainability, ecology

## Cara Penggunaan

### 1. Migration Sudah Dijalankan ✅
```bash
php artisan migrate --path=database/migrations/2025_10_02_000001_create_categories_table.php
php artisan migrate --path=database/migrations/2025_10_02_000002_add_category_id_to_posts_table.php
```

### 2. Reset & Seed Categories ✅
```bash
# Update posts category_id to null first
php artisan tinker --execute="\App\Models\Post::query()->update(['category_id' => null]);"

# Delete old categories
php artisan tinker --execute="\App\Models\Category::query()->delete();"

# Seed new 5 categories
php artisan db:seed --class=CategorySeeder
```

### 3. Fitur yang Ditambahkan ✅

#### Form New Post & Edit Post
- Dropdown category dengan icon dan warna
- Required field
- Menampilkan 5 kategori utama

#### Tampilan Card Post (views.blade.php)
- Badge category dengan icon dan warna dinamis
- Fallback "Umum" jika tidak ada category

#### Detail Post (detail.blade.php)
- Badge category dengan background color transparan
- Menampilkan icon dan nama category
- Terintegrasi dengan meta information

## Model Relationships

### Category Model
```php
public function posts()
{
    return $this->hasMany(Post::class);
}
```

### Post Model
```php
public function category()
{
    return $this->belongsTo(Category::class);
}
```

## Keunggulan 5 Kategori Ini

✅ **Sederhana & Mudah Dipahami** - Tidak terlalu banyak pilihan yang membingungkan
✅ **Universal** - Mencakup topik-topik umum yang relevan
✅ **Terorganisir** - Setiap kategori punya fokus yang jelas
✅ **Visual Menarik** - Icon dan warna yang eye-catching
✅ **Fleksibel** - Bisa menampung berbagai jenis konten

## Catatan
- Setiap kategori memiliki warna unik untuk visual identity
- Icon menggunakan emoji untuk konsistensi di semua platform
- Foreign key menggunakan `onDelete('set null')` sehingga jika kategori dihapus, post tidak ikut terhapus
- Dropdown di form sudah sorted by name untuk kemudahan pencarian
- Total hanya 5 kategori untuk menjaga kesederhanaan
