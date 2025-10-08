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
});
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

    // Visit Management
    Route::get('/visits', [AdminVisitController::class, 'index'])->name('admin.visits.index');
    Route::get('/visits/create', [AdminVisitController::class, 'create'])->name('admin.visits.create');
    Route::post('/visits', [AdminVisitController::class, 'store'])->name('admin.visits.store');
    Route::get('/visits/{visit}', [AdminVisitController::class, 'show'])->name('admin.visits.show');
    Route::get('/visits/{visit}/edit', [AdminVisitController::class, 'edit'])->name('admin.visits.edit');
    Route::put('/visits/{visit}', [AdminVisitController::class, 'update'])->name('admin.visits.update');
    Route::post('/visits/{visit}/status', [AdminVisitController::class, 'updateStatus'])->name('admin.visits.status');
    Route::post('/visits/{visit}/approve', [AdminVisitController::class, 'approve'])->name('admin.visits.approve');
    Route::post('/visits/{visit}/reject', [AdminVisitController::class, 'reject'])->name('admin.visits.reject');
    
    // Visit report approval
    Route::post('/visits/{visit}/approve-report', [AdminVisitController::class, 'approveReport'])->name('admin.visits.approve-report');
    Route::post('/visits/{visit}/reject-report', [AdminVisitController::class, 'rejectReport'])->name('admin.visits.reject-report');
    Route::delete('/visits/{visit}/photo/{photoIndex}', [AdminVisitController::class, 'removePhoto'])->name('admin.visits.photo.remove');
    Route::delete('/visits/{visit}', [AdminVisitController::class, 'destroy'])->name('admin.visits.destroy');
    Route::patch('/visits/{visit}/cancel', [AdminVisitController::class, 'cancel'])->name('admin.visits.cancel');

    // Visit Map
    Route::get('/visits-map', [AdminVisitMapController::class, 'index'])->name('admin.visits.map');

    // Visit Reports & Statistics
    Route::get('/visits-reports', [AdminVisitReportController::class, 'index'])->name('admin.visits.reports');
    Route::get('/visits-export', [AdminVisitReportController::class, 'export'])->name('admin.visits.export');
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
    
    // Author workflow routes
    // Author Actions - New Logic Flow
    Route::patch('/visits/{visit}/confirm', [AuthorVisitActionController::class, 'confirm'])->name('author.visits.confirm');
    Route::patch('/visits/{visit}/reschedule', [AuthorVisitActionController::class, 'reschedule'])->name('author.visits.reschedule');
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
    Route::patch('/visits/{visit}/complete', [AuditorVisitActionController::class, 'complete'])->name('auditor.visits.complete');
    
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
