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
            // Drop old foreign keys if exists
            $table->dropForeign(['auditor_id']);
            $table->dropForeign(['author_id']);
            
            // Remove old columns
            $table->dropColumn(['auditor_id', 'author_id']);
            
            // Add new foreign key columns
            $table->string('auditor_id')->nullable()->after('auditor_name');
            $table->string('author_id')->nullable()->after('author_name');
            
            // Add new foreign key constraints
            $table->foreign('auditor_id')->references('id')->on('auditors')->onDelete('set null');
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // Drop new foreign keys
            $table->dropForeign(['auditor_id']);
            $table->dropForeign(['author_id']);
            
            // Remove new columns
            $table->dropColumn(['auditor_id', 'author_id']);
            
            // Add back old columns (restore to users table structure)
            $table->string('auditor_id')->nullable()->after('auditor_name');
            $table->string('author_id')->nullable()->after('author_name');
            
            // Add back old foreign keys to users table
            $table->foreign('auditor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('author_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
