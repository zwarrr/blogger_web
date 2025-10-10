<?php
// Temporary test route for debugging
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Author\AuthorVisitController;

Route::get('/test-visits-view', function () {
    // Create fake data to test the view
    return view('author.visits.index', [
        'totalVisits' => 25,
        'belumDikunjungi' => 8,
        'dalamPerjalanan' => 3,
        'selesai' => 14,
        'visits' => collect([
            (object)[
                'id' => 1,
                'auditor_name' => 'Ahmad Rizki',
                'status' => 'menunggu'
            ],
            (object)[
                'id' => 2,
                'auditor_name' => 'Siti Nurhaliza', 
                'status' => 'selesai'
            ]
        ])
    ]);
});
