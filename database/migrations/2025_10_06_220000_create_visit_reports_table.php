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
        Schema::create('visit_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visit_id');
            $table->text('hasil_kunjungan')->comment('Hasil kunjungan yang diisi auditor');
            $table->string('foto_selfie')->comment('Path foto selfie auditor di lokasi');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Koordinat latitude saat foto');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Koordinat longitude saat foto');
            $table->text('catatan_revisi')->nullable()->comment('Catatan revisi dari admin');
            $table->enum('status_laporan', ['pending', 'approved', 'revision'])->default('pending');
            $table->timestamp('submitted_at')->nullable()->comment('Waktu laporan dikirim');
            $table->timestamp('reviewed_at')->nullable()->comment('Waktu admin review');
            $table->string('reviewed_by')->nullable()->comment('ID admin yang review');
            $table->timestamps();

            $table->foreign('visit_id')->references('id')->on('visits')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['visit_id', 'status_laporan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_reports');
    }
};