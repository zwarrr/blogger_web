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
        // Drop table if exists and recreate
        Schema::dropIfExists('visit_reports');
        
        Schema::create('visit_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visit_id');
            $table->string('auditor_id');
            $table->date('tanggal_kunjungan_aktual');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->text('lokasi_kunjungan');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('hasil_kunjungan');
            $table->text('temuan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->enum('status_kunjungan', ['berhasil', 'tidak_berhasil', 'tertunda']);
            $table->text('kendala')->nullable();
            $table->json('foto_kunjungan')->nullable();
            $table->json('dokumen_pendukung')->nullable();
            $table->text('catatan_auditor')->nullable();
            $table->enum('status', ['submitted', 'approved', 'revision_required'])->default('submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('visit_id')->references('id')->on('visits')->onDelete('cascade');
            $table->foreign('auditor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
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
