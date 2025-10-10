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
        // Clear all existing visit data (including dummy/test data)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('visit_reports')->truncate();
        DB::table('visits')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Update visits table to ensure visit_id column exists and is properly configured
        Schema::table('visits', function (Blueprint $table) {
            // Make sure visit_id column exists and is properly indexed
            if (!Schema::hasColumn('visits', 'visit_id')) {
                $table->string('visit_id')->unique()->after('id');
            }
            
            // Ensure proper indexing
            $table->index(['visit_id']);
            $table->index(['status']);
            $table->index(['author_id']);
            $table->index(['auditor_id']);
            $table->index(['visit_date']);
        });
        
        // Reset auto increment for visits table
        DB::statement('ALTER TABLE visits AUTO_INCREMENT = 1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // Remove indexes if they exist
            $table->dropIndex(['visit_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['author_id']);
            $table->dropIndex(['auditor_id']);
            $table->dropIndex(['visit_date']);
        });
    }
};
