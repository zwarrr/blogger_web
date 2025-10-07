<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // First, drop any existing foreign keys that might reference users table
            try {
                $table->dropForeign(['auditor_id']);
            } catch (Exception $e) {
                // Ignore if foreign key doesn't exist
            }
            
            try {
                $table->dropForeign(['author_id']);
            } catch (Exception $e) {
                // Ignore if foreign key doesn't exist
            }
            
            // Add auditor_id column if it doesn't exist
            if (!Schema::hasColumn('visits', 'auditor_id')) {
                $table->string('auditor_id')->nullable()->after('auditor_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // Don't do anything in rollback to avoid issues
        });
    }
};
