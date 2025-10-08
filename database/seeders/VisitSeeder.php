<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Visit;
use Carbon\Carbon;

class VisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $visits = [
            [
                'visit_id' => 'VIS-20251006-0001',
                'author_name' => 'Ahmad Wijaya',
                'auditor_name' => 'Budi Santoso',
                'location_address' => 'Jl. Sudirman No. 45, Jakarta Pusat, DKI Jakarta 10110',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'status' => 'completed',
                'notes' => 'Kunjungan berjalan lancar. Author sangat kooperatif dan menyediakan dokumentasi lengkap.',
                'visit_date' => Carbon::now()->subDays(5),
            ],
            [
                'visit_id' => 'VIS-20251006-0002',
                'author_name' => 'Siti Nurhaliza',
                'auditor_name' => 'Dwi Permata',
                'location_address' => 'Jl. Malioboro No. 123, Yogyakarta, DIY 55271',
                'latitude' => -7.7956,
                'longitude' => 110.3695,
                'status' => 'confirmed',
                'notes' => 'Menunggu konfirmasi dari author terkait waktu kunjungan.',
                'visit_date' => Carbon::now()->addDays(3),
            ],
            [
                'visit_id' => 'VIS-20251006-0003',
                'author_name' => 'Rizki Pratama',
                'auditor_name' => 'Andi Wijaya',
                'location_address' => 'Jl. Asia Afrika No. 67, Bandung, Jawa Barat 40111',
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'status' => 'pending',
                'notes' => 'Kunjungan pertama ke lokasi ini. Perlu persiapan ekstra.',
                'visit_date' => Carbon::now()->addDays(7),
            ],
            [
                'visit_id' => 'VIS-20251006-0004',
                'author_name' => 'Dina Marlina',
                'auditor_name' => 'Hendra Gunawan',
                'location_address' => 'Jl. Tunjungan No. 89, Surabaya, Jawa Timur 60261',
                'latitude' => -7.2575,
                'longitude' => 112.7521,
                'status' => 'completed',
                'notes' => 'Audit selesai dengan hasil memuaskan. Semua dokumen telah diperiksa.',
                'visit_date' => Carbon::now()->subDays(2),
            ],
            [
                'visit_id' => 'VIS-20251006-0005',
                'author_name' => 'Fajar Ramadhan',
                'auditor_name' => 'Linda Sari',
                'location_address' => 'Jl. Diponegoro No. 234, Semarang, Jawa Tengah 50241',
                'latitude' => -6.9666,
                'longitude' => 110.4170,
                'status' => 'confirmed',
                'notes' => 'Perlu koordinasi lebih lanjut terkait dokumentasi yang diperlukan.',
                'visit_date' => Carbon::now()->addDays(5),
            ],
            [
                'visit_id' => 'VIS-20251006-0006',
                'author_name' => 'Maya Indira',
                'auditor_name' => 'Rudi Hartono',
                'location_address' => 'Jl. Gajah Mada No. 456, Medan, Sumatera Utara 20111',
                'latitude' => 3.5952,
                'longitude' => 98.6722,
                'status' => 'pending',
                'notes' => 'Menunggu konfirmasi ketersediaan author.',
                'visit_date' => Carbon::now()->addDays(10),
            ],
            [
                'visit_id' => 'VIS-20251006-0007',
                'author_name' => 'Teguh Prasetyo',
                'auditor_name' => 'Sri Wahyuni',
                'location_address' => 'Jl. Imam Bonjol No. 78, Denpasar, Bali 80119',
                'latitude' => -8.6500,
                'longitude' => 115.2167,
                'status' => 'completed',
                'notes' => 'Kunjungan berhasil. Author memberikan feedback positif tentang proses audit.',
                'visit_date' => Carbon::now()->subDays(8),
            ],
            [
                'visit_id' => 'VIS-20251006-0008',
                'author_name' => 'Indah Permatasari',
                'auditor_name' => 'Joko Susilo',
                'location_address' => 'Jl. Ahmad Yani No. 321, Makassar, Sulawesi Selatan 90111',
                'latitude' => -5.1477,
                'longitude' => 119.4327,
                'status' => 'pending',
                'notes' => 'Koordinasi awal telah dilakukan. Menunggu jadwal yang tepat.',
                'visit_date' => Carbon::now()->addDays(12),
            ],
        ];

        foreach ($visits as $visitData) {
            Visit::create($visitData);
        }
    }
}