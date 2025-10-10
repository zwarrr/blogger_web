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
        // Ambil users berdasarkan role
        $authors = \App\Models\User::where('role', 'author')->get();
        $auditors = \App\Models\User::where('role', 'auditor')->get();
        $admins = \App\Models\User::where('role', 'admin')->get();
        
        if ($authors->isEmpty() || $auditors->isEmpty() || $admins->isEmpty()) {
            echo "Tidak ada user dengan role author, auditor, atau admin. Silakan buat user terlebih dahulu.\n";
            return;
        }

        $visits = [
            [
                'author_name' => $authors[0]->name,
                'author_id' => $authors[0]->id,
                'auditor_name' => $auditors[0]->name,
                'auditor_id' => $auditors[0]->id,
                'location_address' => 'Jl. Sudirman No. 45, Jakarta Pusat, DKI Jakarta 10110',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'status' => 'selesai',
                'notes' => 'Kunjungan berjalan lancar. Author sangat kooperatif dan menyediakan dokumentasi lengkap.',
                'visit_date' => Carbon::now()->subDays(5),
                'visit_purpose' => 'Audit rutin konten blog',
                'created_by' => $admins[0]->id,
                'reschedule_count' => 0,
            ],
            [
                'author_name' => $authors[1]->name ?? $authors[0]->name,
                'author_id' => $authors[1]->id ?? $authors[0]->id,
                'auditor_name' => $auditors[1]->name ?? $auditors[0]->name,
                'auditor_id' => $auditors[1]->id ?? $auditors[0]->id,
                'location_address' => 'Jl. Malioboro No. 123, Yogyakarta, DIY 55271',
                'latitude' => -7.7956,
                'longitude' => 110.3695,
                'status' => 'belum_dikunjungi',
                'notes' => 'Menunggu konfirmasi dari author terkait waktu kunjungan.',
                'visit_date' => Carbon::now()->addDays(3),
                'visit_purpose' => 'Review konten dan SEO',
                'created_by' => $admins[0]->id,
                'reschedule_count' => 0,
            ],
            [
                'author_name' => $authors[2]->name ?? $authors[0]->name,
                'author_id' => $authors[2]->id ?? $authors[0]->id,
                'auditor_name' => $auditors[2]->name ?? $auditors[0]->name,
                'auditor_id' => $auditors[2]->id ?? $auditors[0]->id,
                'location_address' => 'Jl. Asia Afrika No. 67, Bandung, Jawa Barat 40111',
                'latitude' => -6.9175,
                'longitude' => 107.6191,
                'status' => 'dalam_perjalanan',
                'notes' => 'Kunjungan pertama ke lokasi ini. Perlu persiapan ekstra.',
                'visit_date' => Carbon::now()->addDays(1),
                'visit_purpose' => 'Audit keamanan konten',
                'created_by' => $admins[0]->id,
                'reschedule_count' => 0,
            ]
        ];

        foreach ($visits as $visitData) {
            Visit::create($visitData);
        }
    }
}