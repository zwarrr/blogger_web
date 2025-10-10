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
        // Drop tabel yang tidak diperlukan
        $tablesToDrop = [
            'visitor_stats',
            'visit_reports' // Jika ada dan tidak digunakan
        ];
        
        foreach ($tablesToDrop as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu membuat ulang tabel yang dihapus
        // karena tabel ini memang tidak digunakan
    }
};