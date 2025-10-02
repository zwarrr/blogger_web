<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tutorial & Tips',
                'icon' => '',
                'color' => '#3B82F6',
                'description' => 'Panduan langkah demi langkah dan tips berguna'
            ],
            [
                'name' => 'Review & Opini',
                'icon' => '',
                'color' => '#F59E0B',
                'description' => 'Ulasan produk, layanan, dan opini pribadi'
            ],
            [
                'name' => 'Teknologi & Aplikasi',
                'icon' => '',
                'color' => '#8B5CF6',
                'description' => 'Berita dan informasi seputar teknologi dan aplikasi'
            ],
            [
                'name' => 'Edukasi & Informasi',
                'icon' => '',
                'color' => '#10B981',
                'description' => 'Konten edukatif dan informasi bermanfaat'
            ],
            [
                'name' => 'Alam & Lingkungan',
                    'icon' => '',
                'color' => '#059669',
                'description' => 'Topik seputar alam, lingkungan, dan keberlanjutan'
            ],
        ];

        foreach ($categories as $category) {
            $slug = Str::slug($category['name']);
            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'description' => $category['description'],
                ]
            );
        }
    }
}
