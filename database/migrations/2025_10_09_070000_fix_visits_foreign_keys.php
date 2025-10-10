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
            // Hapus foreign key constraints yang lama jika ada
            try {
                $table->dropForeign(['author_id']);
            } catch (Exception $e) {
                // Ignore if doesn't exist
            }
            
            try {
                $table->dropForeign(['auditor_id']);
            } catch (Exception $e) {
                // Ignore if doesn't exist
            }
            
            try {
                $table->dropForeign(['created_by']);
            } catch (Exception $e) {
                // Ignore if doesn't exist
            }
            
            try {
                $table->dropForeign(['confirmed_by']);
            } catch (Exception $e) {
                // Ignore if doesn't exist
            }
            
            try {
                $table->dropForeign(['rescheduled_by']);
            } catch (Exception $e) {
                // Ignore if doesn't exist
            }
        });
        
        // Pastikan kolom yang diperlukan ada
        Schema::table('visits', function (Blueprint $table) {
            if (!Schema::hasColumn('visits', 'author_id')) {
                $table->string('author_id')->nullable()->after('author_name');
            }
            
            if (!Schema::hasColumn('visits', 'auditor_id')) {
                $table->string('auditor_id')->nullable()->after('auditor_name');
            }
            
            if (!Schema::hasColumn('visits', 'confirmed_by')) {
                $table->string('confirmed_by')->nullable()->after('confirmed_at');
            }
            
            if (!Schema::hasColumn('visits', 'rescheduled_by')) {
                $table->string('rescheduled_by')->nullable()->after('rescheduled_at');
            }
        });

        // Tambahkan foreign key constraints yang benar
        Schema::table('visits', function (Blueprint $table) {
            // Foreign key ke users table
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('auditor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('confirmed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rescheduled_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropForeign(['auditor_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['confirmed_by']);
            $table->dropForeign(['rescheduled_by']);
        });
    }
};