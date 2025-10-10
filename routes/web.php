<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminCommentController;

use App\Http\Controllers\Auth\AuthorAuthController;
use App\Http\Controllers\AuthorDashboardController;
use App\Http\Controllers\AuthorPostController;
use App\Http\Controllers\Admin\AdminVisitController;
use App\Http\Controllers\Admin\AdminVisitMapController;
use App\Http\Controllers\Admin\AdminVisitReportController;
use App\Http\Controllers\AuditorDashboardController;
use App\Http\Controllers\Auditor\AuditorVisitController;
use App\Http\Controllers\Auditor\AuditorVisitActionController;
use App\Http\Controllers\Author\AuthorVisitController;
use App\Http\Controllers\Author\AuthorVisitActionController;
use App\Http\Controllers\AuditorAuthController;
use App\Http\Controllers\AuthController;

// Make posts list the homepage
Route::get('/', [UserDashboardController::class, 'views'])->name('user.views');

Route::get('/test-author-login', function () {
    return view('test-author-login');
});

// Test route for debugging admin visits (remove after testing)
// Route::get('/test-admin-visits', [AdminVisitController::class, 'index'])->name('test.admin.visits');

// Temporary test route for debugging the visits view
Route::get('/test-visits-view', function () {
    // Create mock paginated visits data
    $visitsData = collect([
        (object)[
            'id' => 1,
            'visit_date' => '2025-10-15 10:00:00',
            'visit_time' => '10:00:00',
            'location_address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            'status' => 'belum_dikunjungi',
            'author_id' => 1,
            'author_name' => 'Budi Santoso'
        ],
        (object)[
            'id' => 2,
            'visit_date' => '2025-10-16 14:00:00',
            'visit_time' => '14:00:00',
            'location_address' => 'Jl. Thamrin No. 456, Jakarta Pusat',
            'status' => 'dalam_perjalanan',
            'author_id' => 1,
            'author_name' => 'Budi Santoso'
        ],
        (object)[
            'id' => 3,
            'visit_date' => '2025-10-17 09:00:00',
            'visit_time' => '09:00:00',
            'location_address' => 'Jl. Gatot Subroto No. 789, Jakarta Selatan',
            'status' => 'selesai',
            'author_id' => 1,
            'author_name' => 'Budi Santoso'
        ]
    ]);

    // Create a mock paginator
    $visits = new \Illuminate\Pagination\LengthAwarePaginator(
        $visitsData,
        3,
        10,
        1,
        ['path' => request()->url()]
    );

    return view('author.visits.index', [
        'totalVisits' => 3,
        'belumDikunjungi' => 1,
        'dalamPerjalanan' => 1,
        'selesai' => 1,
        'sedangDikunjungi' => 0,
        'menungguAcc' => 0,
        'visits' => $visits,
        'statuses' => ['belum_dikunjungi', 'dalam_perjalanan', 'selesai']
    ]);
});

// Quick access to author visits without login (for testing only)
Route::get('/author-visits-demo', function () {
    $visitsData = collect([
        (object)[
            'id' => 1,
            'visit_date' => '2025-10-15 10:00:00',
            'visit_time' => '10:00:00',
            'location_address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            'status' => 'belum_dikunjungi',
            'author_id' => 1,
            'author_name' => 'Budi Santoso'
        ],
        (object)[
            'id' => 2,
            'visit_date' => '2025-10-16 14:00:00',
            'visit_time' => '14:00:00',
            'location_address' => 'Jl. Thamrin No. 456, Jakarta Pusat',
            'status' => 'dalam_perjalanan',
            'author_id' => 1,
            'author_name' => 'Budi Santoso'
        ]
    ]);

    $visits = new \Illuminate\Pagination\LengthAwarePaginator(
        $visitsData, 2, 10, 1, ['path' => request()->url()]
    );

    return view('author.visits.index', [
        'totalVisits' => 2, 'belumDikunjungi' => 1, 'dalamPerjalanan' => 1, 
        'selesai' => 0, 'sedangDikunjungi' => 0, 'menungguAcc' => 0,
        'visits' => $visits, 'statuses' => ['belum_dikunjungi', 'dalam_perjalanan', 'selesai']
    ]);
});

// Test route untuk demo edit page dengan Leaflet map
Route::get('/test-edit-visit', function () {
    // Create a mock visit object for testing
    $visit = (object) [
        'id' => 1,
        'author_name' => 'John Doe',
        'auditor_name' => 'Jane Smith', 
        'location_address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
        'latitude' => -6.208763,
        'longitude' => 106.845599,
        'visit_date' => now(),
        'status' => 'confirmed',
        'notes' => 'Ini adalah catatan demo untuk testing halaman edit dengan Leaflet map',
        'photos' => ['demo1.jpg', 'demo2.jpg'],
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    return view('admin.visits.edit', compact('visit'));
});

Route::get('/authors-table-data', function () {
    $authors = App\Models\Author::orderBy('created_at', 'desc')->get();
    return view('authors-table-data', compact('authors'));
});

// Route untuk refresh CSRF token
Route::get('/refresh-csrf', function() {
    return response()->json([
        'token' => csrf_token()
    ]);
})->name('refresh.csrf');

// Simple test login untuk debugging








// Public blog routes (user can only view list and detail)
// Keep /posts for backward compatibility but redirect to root so URL stays clean
Route::get('/posts', function () { return redirect()->route('user.views'); });
Route::get('/posts/{id}', [UserDashboardController::class, 'detail'])->name('user.detail');
Route::post('/tracker', [UserDashboardController::class, 'tracker'])->name('user.tracker');
Route::post('/posts/{id}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/posts/{id}/comments/{commentId}/reply', [CommentController::class, 'reply'])->name('comments.reply');
Route::post('/posts/{id}/comments/{commentId}/like', [CommentController::class, 'like'])->name('comments.like');

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.submit');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});



// Redirect old login URLs to unified login
Route::get('/login', function() {
    return redirect()->route('auth.login');
})->name('login');
Route::get('/admin/login', function() {
    return redirect()->route('auth.login');
});
Route::get('/auditor/login', function() {
    return redirect()->route('auth.login');
})->name('auditor.login');
Route::get('/author/login', function() {
    return redirect()->route('auth.login');
});

// Admin protected routes
// (Authentication handled by unified auth system)

Route::prefix('admin')->middleware(['web', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/posts', [AdminPostController::class, 'index'])->name('admin.posts.index');
    Route::post('/posts', [AdminPostController::class, 'store'])->name('admin.posts.store');
    Route::put('/posts/{id}', [AdminPostController::class, 'update'])->name('admin.posts.update');
    Route::delete('/posts/{id}', [AdminPostController::class, 'destroy'])->name('admin.posts.destroy');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // Comments
    Route::get('/comments', [AdminCommentController::class, 'index'])->name('admin.comments.index');
    Route::post('/comments/{id}/toggle', [AdminCommentController::class, 'toggleVisibility'])->name('admin.comments.toggle');
    Route::delete('/comments/{id}', [AdminCommentController::class, 'destroy'])->name('admin.comments.destroy');

    // Log Management - Coming Soon
    // Route::get('/logs/error', [AdminLogController::class, 'errorLogs'])->name('admin.logs.error');

    // Visit Management - specific routes first, then parameterized routes
    Route::get('/visits', [AdminVisitController::class, 'index'])->name('admin.visits.index');
    Route::get('/visits/create', [AdminVisitController::class, 'create'])->name('admin.visits.create');
    Route::post('/visits', [AdminVisitController::class, 'store'])->name('admin.visits.store');
    Route::get('/visits/{visit}/json', [AdminVisitController::class, 'showJson'])->name('admin.visits.show.json')->where('visit', '[0-9]+');
    Route::get('/visits/{visit}/edit', [AdminVisitController::class, 'edit'])->name('admin.visits.edit')->where('visit', '[0-9]+');
    Route::get('/visits/{visit}', [AdminVisitController::class, 'show'])->name('admin.visits.show')->where('visit', '[0-9]+');
    Route::put('/visits/{visit}', [AdminVisitController::class, 'update'])->name('admin.visits.update')->where('visit', '[0-9]+');
    Route::post('/visits/{visit}/status', [AdminVisitController::class, 'updateStatus'])->name('admin.visits.status')->where('visit', '[0-9]+');
    Route::post('/visits/{visit}/approve', [AdminVisitController::class, 'approve'])->name('admin.visits.approve')->where('visit', '[0-9]+');
    Route::post('/visits/{visit}/reject', [AdminVisitController::class, 'reject'])->name('admin.visits.reject')->where('visit', '[0-9]+');
    Route::post('/visits/{visit}/complete', [AdminVisitController::class, 'complete'])->name('admin.visits.complete')->where('visit', '[0-9]+');
    
    // Visit report approval
    Route::post('/visits/{visit}/approve-report', [AdminVisitController::class, 'approveReport'])->name('admin.visits.approve-report')->where('visit', '[0-9]+');
    Route::post('/visits/{visit}/reject-report', [AdminVisitController::class, 'rejectReport'])->name('admin.visits.reject-report')->where('visit', '[0-9]+');
    Route::delete('/visits/{visit}/photo/{photoIndex}', [AdminVisitController::class, 'removePhoto'])->name('admin.visits.photo.remove')->where('visit', '[0-9]+');
    Route::delete('/visits/{visit}', [AdminVisitController::class, 'destroy'])->name('admin.visits.destroy')->where('visit', '[0-9]+');
    Route::patch('/visits/{visit}/cancel', [AdminVisitController::class, 'cancel'])->name('admin.visits.cancel')->where('visit', '[0-9]+');

    // Visit Map
    Route::get('/visits-map', [AdminVisitMapController::class, 'index'])->name('admin.visits.map');
    
    // Debug route untuk check data Visit
    Route::get('/debug/visits', function() {
        $visits = \App\Models\Visit::with(['author', 'auditor'])->get();
        
        $debug = [
            'total_visits' => $visits->count(),
            'visits_with_coords' => $visits->filter(function($visit) {
                return $visit->latitude && $visit->longitude && 
                       $visit->latitude != 0 && $visit->longitude != 0;
            })->count(),
            'sample_visits' => $visits->take(5)->map(function($visit) {
                return [
                    'id' => $visit->id,
                    'visit_id' => $visit->visit_id,
                    'author' => $visit->author ? $visit->author->name : null,
                    'auditor' => $visit->auditor ? $visit->auditor->name : null,
                    'latitude' => $visit->latitude,
                    'longitude' => $visit->longitude,
                    'location_address' => $visit->location_address,
                    'status' => $visit->status,
                    'created_at' => $visit->created_at
                ];
            }),
            'status_distribution' => $visits->groupBy('status')->map->count()
        ];
        
        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
    });

    // Visit Reports & Statistics
    Route::get('/visits-reports', [AdminVisitReportController::class, 'index'])->name('admin.visits.reports');
    Route::get('/visits-export', [AdminVisitReportController::class, 'export'])->name('admin.visits.export');
    Route::get('/visits-export-pdf', [AdminVisitReportController::class, 'exportToPdf'])->name('admin.visits.export-pdf');
});

// Author protected routes
Route::prefix('author')->middleware(['web', 'author'])->group(function () {
    Route::get('/dashboard', [AuthorDashboardController::class, 'index'])->name('author.dashboard');
    
    // Author Posts routes
    Route::get('/posts', [AuthorPostController::class, 'index'])->name('author.posts.index');
    Route::get('/posts/create', [AuthorPostController::class, 'create'])->name('author.posts.create');
    Route::post('/posts', [AuthorPostController::class, 'store'])->name('author.posts.store');
    Route::get('/posts/{post}/edit', [AuthorPostController::class, 'edit'])->name('author.posts.edit');
    Route::put('/posts/{post}', [AuthorPostController::class, 'update'])->name('author.posts.update');
    Route::delete('/posts/{post}', [AuthorPostController::class, 'destroy'])->name('author.posts.destroy');
    
    // Author Visit routes
    Route::get('/visits', [AuthorVisitController::class, 'index'])->name('author.visits.index');
    Route::get('/visits/{visit}', [AuthorVisitController::class, 'show'])->name('author.visits.show');
    Route::get('/visits/{visit}/detail', [AuthorVisitController::class, 'detail'])->name('author.visits.detail');
    Route::get('/visits/{visit}/modal-detail', [AuthorVisitController::class, 'getModalDetail'])->name('author.visits.modal-detail');
    
    // Author workflow routes
    // Author Actions - New Logic Flow
    Route::patch('/visits/{visit}/confirm', [AuthorVisitController::class, 'confirm'])->name('author.visits.confirm');
    Route::patch('/visits/{visit}/reschedule', [AuthorVisitController::class, 'reschedule'])->name('author.visits.reschedule');
});

// Auditor protected routes
// (Authentication handled by unified auth system)
Route::prefix('auditor')->middleware(['web', 'auditor'])->group(function () {
    Route::get('/dashboard', [AuditorDashboardController::class, 'index'])->name('auditor.dashboard');
    
    // Visit management for Auditors
    Route::get('/visits', [AuditorVisitController::class, 'index'])->name('auditor.visits.index');
    Route::get('/visits/{visit}', [AuditorVisitController::class, 'show'])->name('auditor.visits.show');
    Route::get('/visits/{visit}/detail', [AuditorVisitController::class, 'detail'])->name('auditor.visits.detail');
    
    // Visit workflow management
    // Auditor Actions - New Logic Flow
    Route::patch('/visits/{visit}/start', [AuditorVisitController::class, 'start'])->name('auditor.visits.start');
    Route::match(['POST', 'PATCH'], '/visits/{visit}/complete', [AuditorVisitActionController::class, 'complete'])->name('auditor.visits.complete');
    
    // Visit status management (legacy)
    Route::patch('/visits/{visit}/status', [AuditorVisitController::class, 'updateStatus'])->name('auditor.visits.update-status');
    
    // Visit reporting
    Route::get('/visits/{visit}/create-report', [AuditorVisitController::class, 'createReport'])->name('auditor.visits.create-report');
    Route::post('/visits/{visit}/store-report', [AuditorVisitController::class, 'storeReport'])->name('auditor.visits.store-report');
    Route::get('/visits/{visit}/show-report', [AuditorVisitController::class, 'showReport'])->name('auditor.visits.show-report');
    
    // Visit map
    Route::get('/visits-map', [AuditorVisitController::class, 'map'])->name('auditor.visits.map');
    
    // Visit statistics
    Route::get('/visits-statistics', [AuditorVisitController::class, 'statistics'])->name('auditor.visits.statistics');
});

// Error testing routes (only for development)
if (app()->environment(['local', 'testing'])) {
    Route::get('/test-error/{code}', function ($code) {
        $validCodes = [400, 401, 403, 404, 405, 419, 422, 429, 500, 502, 503, 504];
        
        if (!in_array($code, $validCodes)) {
            abort(400, 'Invalid error code. Valid codes: ' . implode(', ', $validCodes));
        }
        
        abort($code, "Testing error {$code}");
    })->name('test.error');
    
    Route::get('/test-errors', function () {
        $errors = [
            400 => 'Bad Request',
            401 => 'Unauthorized', 
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            419 => 'Page Expired',
            422 => 'Unprocessable Entity', 
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout'
        ];
        
        return view('test-errors', compact('errors'));
    })->name('test.errors');
}
