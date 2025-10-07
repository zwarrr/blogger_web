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
        // Step 1: Add new columns first
        Schema::table('visits', function (Blueprint $table) {
            if (!Schema::hasColumn('visits', 'assigned_to')) {
                $table->string('assigned_to')->nullable()->after('auditor_id')->comment('ID Auditor yang ditugaskan');
            }
            
            if (!Schema::hasColumn('visits', 'visit_purpose')) {
                $table->text('visit_purpose')->nullable()->after('visit_date')->comment('Tujuan kunjungan');
            }
            
            if (!Schema::hasColumn('visits', 'report_notes')) {
                $table->text('report_notes')->nullable()->after('notes')->comment('Catatan laporan dari auditor');
            }
            
            if (!Schema::hasColumn('visits', 'selfie_photo')) {
                $table->string('selfie_photo')->nullable()->after('photos')->comment('Foto selfie auditor');
            }
            
            if (!Schema::hasColumn('visits', 'selfie_latitude')) {
                $table->decimal('selfie_latitude', 10, 8)->nullable()->after('selfie_photo')->comment('Koordinat latitude saat selfie');
            }
            
            if (!Schema::hasColumn('visits', 'selfie_longitude')) {
                $table->decimal('selfie_longitude', 11, 8)->nullable()->after('selfie_latitude')->comment('Koordinat longitude saat selfie');
            }
            
            if (!Schema::hasColumn('visits', 'created_by')) {
                $table->string('created_by')->nullable()->after('selfie_longitude')->comment('Admin yang membuat tugas');
            }
            
            if (!Schema::hasColumn('visits', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('created_by')->comment('Waktu selesai kunjungan');
            }
        });

        // Step 2: Update existing data to compatible values first
        DB::statement("UPDATE visits SET status = 'selesai' WHERE status = 'konfirmasi'");
        
        // Step 3: Change the status column to VARCHAR temporarily
        DB::statement("ALTER TABLE visits MODIFY COLUMN status VARCHAR(50) DEFAULT 'belum_dikunjungi'");
        
        // Step 4: Update status values to new format
        DB::statement("UPDATE visits SET status = 'belum_dikunjungi' WHERE status = 'pending'");
        
        // Step 5: Change back to ENUM with new values
        DB::statement("ALTER TABLE visits MODIFY COLUMN status ENUM('belum_dikunjungi', 'dalam_perjalanan', 'sedang_dikunjungi', 'menunggu_acc', 'selesai') DEFAULT 'belum_dikunjungi'");
        
        // Step 6: Add foreign key constraints if users table exists
        if (Schema::hasTable('users')) {
            Schema::table('visits', function (Blueprint $table) {
                try {
                    $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
                } catch (Exception $e) {
                    // Ignore if foreign key already exists
                }
                
                try {
                    $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                } catch (Exception $e) {
                    // Ignore if foreign key already exists
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // Drop foreign keys first
            try {
                $table->dropForeign(['assigned_to']);
            } catch (Exception $e) {
                // Ignore if foreign key doesn't exist
            }
            
            try {
                $table->dropForeign(['created_by']);
            } catch (Exception $e) {
                // Ignore if foreign key doesn't exist
            }
            
            // Drop new columns
            $table->dropColumn([
                'assigned_to',
                'visit_purpose', 
                'report_notes',
                'selfie_photo',
                'selfie_latitude',
                'selfie_longitude',
                'created_by',
                'completed_at'
            ]);
        });
        
        // Revert status column
        DB::statement("ALTER TABLE visits MODIFY COLUMN status ENUM('pending', 'konfirmasi', 'selesai') DEFAULT 'pending'");
    }
};