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
            // Change confirmed_by from unsignedBigInteger to string to match user ID format
            $table->string('confirmed_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            // Revert back to unsignedBigInteger
            $table->unsignedBigInteger('confirmed_by')->nullable()->change();
        });
    }
};