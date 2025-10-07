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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('visit_id')->unique();
            $table->string('author_name');
            $table->string('auditor_name');
            $table->text('location_address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('status', ['pending', 'konfirmasi', 'selesai'])->default('pending');
            $table->text('notes')->nullable();
            $table->json('photos')->nullable(); // Store photo paths as JSON array
            $table->datetime('visit_date');
            $table->datetime('created_at')->useCurrent();
            $table->datetime('updated_at')->useCurrent();
            
            $table->index(['status', 'visit_date']);
            $table->index('author_name');
            $table->index('auditor_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};