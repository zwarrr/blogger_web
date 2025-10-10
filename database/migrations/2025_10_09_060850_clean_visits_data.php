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
        // Reset auto increment untuk memulai dari 1
        DB::statement('ALTER TABLE visits AUTO_INCREMENT = 1');
        
        // Hapus semua data visits yang mungkin bermasalah
        DB::table('visits')->truncate();
        
        // Reset juga auto increment untuk visit_reports jika ada
        if (Schema::hasTable('visit_reports')) {
            DB::statement('ALTER TABLE visit_reports AUTO_INCREMENT = 1');
            DB::table('visit_reports')->truncate();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
