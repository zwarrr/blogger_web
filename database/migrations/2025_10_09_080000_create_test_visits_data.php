<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ambil users berdasarkan role
        $authors = DB::table('users')->where('role', 'author')->get();
        $auditors = DB::table('users')->where('role', 'auditor')->get();
        $admins = DB::table('users')->where('role', 'admin')->get();
        
        if ($authors->isEmpty() || $auditors->isEmpty() || $admins->isEmpty()) {
            echo "Tidak ada user dengan role author, auditor, atau admin. Silakan buat user terlebih dahulu.\n";
            return;
        }

        // Bersihkan data visits yang ada
        DB::table('visits')->delete();
        
        // Reset auto increment
        DB::statement('ALTER TABLE visits AUTO_INCREMENT = 1');

        // Buat data visits untuk setiap author
        foreach ($authors as $index => $author) {
            $auditor = $auditors->get($index % $auditors->count());
            $admin = $admins->first();
            
            $visitData = [
                'author_name' => $author->name,
                'author_id' => $author->id,
                'auditor_name' => $auditor->name,
                'auditor_id' => $auditor->id,
                'location_address' => 'Alamat Test untuk ' . $author->name,
                'latitude' => -6.2088 + ($index * 0.01),
                'longitude' => 106.8456 + ($index * 0.01),
                'status' => ['belum_dikunjungi', 'dalam_perjalanan', 'selesai'][$index % 3],
                'notes' => 'Kunjungan test untuk author ' . $author->name,
                'visit_date' => now()->addDays($index + 1),
                'visit_purpose' => 'Test audit untuk ' . $author->name,
                'created_by' => $admin->id,
                'reschedule_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            DB::table('visits')->insert($visitData);
        }
        
        echo "Berhasil membuat " . count($authors) . " data visits untuk testing.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('visits')->delete();
    }
};