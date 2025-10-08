<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Visit;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing visits to have custom visit_id format
        $visits = Visit::whereNull('visit_id')
                      ->orWhere('visit_id', '')
                      ->orWhere('visit_id', 'NOT LIKE', 'VIST%')
                      ->orderBy('id')
                      ->get();
        
        $counter = 1;
        foreach ($visits as $visit) {
            $visit->visit_id = 'VIST' . str_pad($counter, 4, '0', STR_PAD_LEFT);
            $visit->saveQuietly(); // Save without triggering events
            $counter++;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally revert visit_ids to empty/null if needed
        Visit::where('visit_id', 'LIKE', 'VIST%')
             ->update(['visit_id' => null]);
    }
};
