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
        Schema::create('authors', function (Blueprint $table) {
            $table->string('id')->primary(); // custom id: AUTHOR001, AUTHOR002, etc
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('bio')->nullable(); // bio author
            $table->text('specialization')->nullable(); // spesialisasi author
            $table->integer('total_posts')->default(0); // jumlah post yang dibuat
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->rememberToken()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'total_posts']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
