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
            // Add foreign key columns
            $table->string('auditor_id')->nullable()->after('auditor_name');
            $table->string('author_id')->nullable()->after('author_name');
            
            // Add foreign key constraints
            $table->foreign('auditor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('author_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['auditor_id']);
            $table->dropForeign(['author_id']);
            
            // Drop columns
            $table->dropColumn(['auditor_id', 'author_id']);
        });
    }
};
