<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateVisitStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing status values to match new enum
        DB::statement("UPDATE visits SET status = 'belum_dikunjungi' WHERE status = 'pending'");
        DB::statement("UPDATE visits SET status = 'selesai' WHERE status = 'konfirmasi'");
        DB::statement("UPDATE visits SET status = 'selesai' WHERE status = 'selesai'");
        
        echo "Visit status values updated successfully.\n";
    }
}