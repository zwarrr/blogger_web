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
        // Update status values from old to new format (if any data exists)
        DB::statement("UPDATE visits SET status = 'belum_dikunjungi' WHERE status = 'pending'");
        DB::statement("UPDATE visits SET status = 'sedang_dikunjungi' WHERE status = 'confirmed'");
        DB::statement("UPDATE visits SET status = 'sedang_dikunjungi' WHERE status = 'in_progress'");
        DB::statement("UPDATE visits SET status = 'selesai' WHERE status = 'completed'");
        DB::statement("UPDATE visits SET status = 'belum_dikunjungi' WHERE status = 'cancelled'");
        
        // Change status column to new enum
        DB::statement("ALTER TABLE visits MODIFY COLUMN status ENUM('belum_dikunjungi', 'dalam_perjalanan', 'sedang_dikunjungi', 'menunggu_acc', 'selesai') DEFAULT 'belum_dikunjungi'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status values
        DB::statement("UPDATE visits SET status = 'pending' WHERE status = 'belum_dikunjungi'");
        DB::statement("UPDATE visits SET status = 'confirmed' WHERE status = 'dalam_perjalanan'");
        DB::statement("UPDATE visits SET status = 'in_progress' WHERE status = 'sedang_dikunjungi'");
        DB::statement("UPDATE visits SET status = 'completed' WHERE status = 'selesai'");
        
        // Revert status column to old enum
        DB::statement("ALTER TABLE visits MODIFY COLUMN status ENUM('pending','confirmed','in_progress','completed','cancelled') DEFAULT 'pending'");
    }
};
