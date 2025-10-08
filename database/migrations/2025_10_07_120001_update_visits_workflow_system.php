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
        Schema::table('visits', function (Blueprint $table) {
            // Add reschedule limit tracking
            if (!Schema::hasColumn('visits', 'reschedule_count')) {
                $table->integer('reschedule_count')->default(0)->after('status')->comment('Jumlah pengunduran jadwal oleh author');
            }
            
            // Add confirmed_at timestamp
            if (!Schema::hasColumn('visits', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable()->after('visit_date')->comment('Waktu konfirmasi oleh author');
            }
            
            // Add confirmed_by
            if (!Schema::hasColumn('visits', 'confirmed_by')) {
                $table->unsignedBigInteger('confirmed_by')->nullable()->after('confirmed_at')->comment('Author yang mengkonfirmasi');
            }
            
            // Add started_at timestamp
            if (!Schema::hasColumn('visits', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('confirmed_by')->comment('Waktu mulai proses oleh auditor');
            }
            
            // Add auditor_notes for separate auditor notes
            if (!Schema::hasColumn('visits', 'auditor_notes')) {
                $table->text('auditor_notes')->nullable()->after('report_notes')->comment('Catatan dari auditor');
            }
        });

        // First, change status to VARCHAR to allow updates
        DB::statement("ALTER TABLE visits MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'");
        
        // Update existing data to match new workflow
        DB::statement("UPDATE visits SET status = 'pending' WHERE status = 'belum_dikunjungi'");
        DB::statement("UPDATE visits SET status = 'in_progress' WHERE status IN ('dalam_perjalanan', 'sedang_dikunjungi')");
        DB::statement("UPDATE visits SET status = 'completed' WHERE status IN ('selesai', 'menunggu_acc')");
        
        // Now change back to ENUM with new values
        DB::statement("ALTER TABLE visits MODIFY COLUMN status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status values first
        DB::statement("ALTER TABLE visits MODIFY COLUMN status VARCHAR(50)");
        DB::statement("UPDATE visits SET status = 'belum_dikunjungi' WHERE status = 'pending'");
        DB::statement("UPDATE visits SET status = 'dalam_perjalanan' WHERE status = 'in_progress'");
        DB::statement("UPDATE visits SET status = 'selesai' WHERE status = 'completed'");
        
        // Revert status enum
        DB::statement("ALTER TABLE visits MODIFY COLUMN status ENUM('belum_dikunjungi', 'dalam_perjalanan', 'sedang_dikunjungi', 'menunggu_acc', 'selesai') DEFAULT 'belum_dikunjungi'");
        
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn([
                'reschedule_count',
                'confirmed_at',
                'confirmed_by',
                'started_at',
                'auditor_notes'
            ]);
        });
    }
};