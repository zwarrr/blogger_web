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
        // Add auditor role to existing enum values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'auditor', 'author', 'user') DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove auditor role from enum values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'author', 'user') DEFAULT 'user'");
    }
};
