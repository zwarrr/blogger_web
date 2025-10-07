<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Visit;
use App\Models\User;
use Carbon\Carbon;

class VisitSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get our test users
        $auditor1 = User::where('id', 'USERBLOG050')->first();
        $auditor2 = User::where('id', 'USERBLOG051')->first();
        $author = User::where('id', 'USERBLOG001')->first();

        if (!$auditor1 || !$auditor2 || !$author) {
            $this->command->error('Required users not found. Please check if AuditorSeeder and UserSeeder have been run.');
            $this->command->info('Looking for:');
            $this->command->info('- Auditor 1: USERBLOG050' . ($auditor1 ? ' ✓' : ' ✗'));
            $this->command->info('- Auditor 2: USERBLOG051' . ($auditor2 ? ' ✓' : ' ✗'));
            $this->command->info('- Author: USERBLOG001' . ($author ? ' ✓' : ' ✗'));
            return;
        }

        // Create sample visits
        $visits = [
            [
                'visit_id' => Visit::generateVisitId(),
                'author_name' => $author->name,
                'author_id' => $author->id,
                'auditor_name' => $auditor1->name,
                'auditor_id' => $auditor1->id,
                'location_address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'status' => 'pending',
                'visit_date' => Carbon::today()->addHours(9),
                'notes' => null,
                'photos' => null,
            ],
            [
                'visit_id' => Visit::generateVisitId(),
                'author_name' => $author->name,
                'author_id' => $author->id,
                'auditor_name' => $auditor1->name,
                'auditor_id' => $auditor1->id,
                'location_address' => 'Jl. Thamrin No. 456, Jakarta Pusat',
                'latitude' => -6.1944,
                'longitude' => 106.8229,
                'status' => 'konfirmasi',
                'visit_date' => Carbon::yesterday()->addHours(10),
                'notes' => 'Kunjungan telah dilakukan dengan baik. Author sangat kooperatif.',
                'photos' => ['visits/sample1.jpg', 'visits/sample2.jpg'],
            ],
            [
                'visit_id' => Visit::generateVisitId(),
                'author_name' => $author->name,
                'author_id' => $author->id,
                'auditor_name' => $auditor2->name,
                'auditor_id' => $auditor2->id,
                'location_address' => 'Jl. Gatot Subroto No. 789, Jakarta Selatan',
                'latitude' => -6.2274,
                'longitude' => 106.8091,
                'status' => 'selesai',
                'visit_date' => Carbon::now()->subDays(2)->addHours(14),
                'notes' => 'Kunjungan selesai. Semua dokumen sudah dikumpulkan.',
                'photos' => ['visits/sample3.jpg'],
            ],
            [
                'visit_id' => Visit::generateVisitId(),
                'author_name' => $author->name,
                'author_id' => $author->id,
                'auditor_name' => $auditor1->name,
                'auditor_id' => $auditor1->id,
                'location_address' => 'Jl. Kuningan No. 321, Jakarta Selatan',
                'latitude' => -6.2297,
                'longitude' => 106.8312,
                'status' => 'pending',
                'visit_date' => Carbon::tomorrow()->addHours(11),
                'notes' => null,
                'photos' => null,
            ],
        ];

        foreach ($visits as $visitData) {
            Visit::create($visitData);
        }

        $this->command->info('Sample visit data created successfully!');
    }
}
