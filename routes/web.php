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

// Make posts list the homepage
Route::get('/', [UserDashboardController::class, 'views'])->name('user.views');

// Test animation route
Route::get('/test-animation', function () {
    return view('test-animation');
})->name('test.animation');

// Test loading route
Route::get('/test-loading', function () {
    return view('test-loading');
})->name('test.loading');

// Public blog routes (user can only view list and detail)
// Keep /posts for backward compatibility but redirect to root so URL stays clean
Route::get('/posts', function () { return redirect()->route('user.views'); });
Route::get('/posts/{id}', [UserDashboardController::class, 'detail'])->name('user.detail');
Route::post('/tracker', [UserDashboardController::class, 'tracker'])->name('user.tracker');
Route::post('/posts/{id}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/posts/{id}/comments/{commentId}/reply', [CommentController::class, 'reply'])->name('comments.reply');
Route::post('/posts/{id}/comments/{commentId}/like', [CommentController::class, 'like'])->name('comments.like');

// Admin login uses the shared auth/login view
Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
    ->middleware('guest:admin')
    ->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])
    ->middleware('guest:admin')
    ->name('login.post');

// Admin area (protected by admin guard)
Route::prefix('admin')->group(function () {
    // Handle admin logout
    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth:admin')
        ->name('admin.logout');
});

Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/posts', [AdminPostController::class, 'index'])->name('admin.posts.index');
    Route::post('/posts', [AdminPostController::class, 'store'])->name('admin.posts.store');
    Route::put('/posts/{id}', [AdminPostController::class, 'update'])->name('admin.posts.update');
    Route::delete('/posts/{id}', [AdminPostController::class, 'destroy'])->name('admin.posts.destroy');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');

    // Comments
    Route::get('/comments', [AdminCommentController::class, 'index'])->name('admin.comments.index');
    Route::post('/comments/{id}/toggle', [AdminCommentController::class, 'toggleVisibility'])->name('admin.comments.toggle');
    Route::delete('/comments/{id}', [AdminCommentController::class, 'destroy'])->name('admin.comments.destroy');
});

// No user login/registration; users only view public pages

// Author authentication (uses default web guard with role check)
Route::prefix('author')->group(function () {
    // Login & Register pages for authors (reuse auth views)
    Route::get('/login', [AuthorAuthController::class, 'showLoginForm'])->name('author.login');
    Route::post('/login', [AuthorAuthController::class, 'login'])->name('author.login.post');
    Route::get('/register', [AuthorAuthController::class, 'showRegisterForm'])->name('author.register');
    Route::post('/register', [AuthorAuthController::class, 'register'])->name('author.register.post');

    // Logout (must be authenticated via web)
    Route::post('/logout', [AuthorAuthController::class, 'logout'])->name('author.logout');
});

// Preferred clean URLs for author auth under /auth/*
Route::prefix('auth')->group(function () {
    Route::get('/login', [AuthorAuthController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [AuthorAuthController::class, 'login'])->name('auth.login.post');
    Route::get('/register', [AuthorAuthController::class, 'showRegisterForm'])->name('auth.register');
    Route::post('/register', [AuthorAuthController::class, 'register'])->name('auth.register.post');
    Route::post('/logout', [AuthorAuthController::class, 'logout'])->name('auth.logout');
});

// Author area (requires logged-in user with role=author)
Route::prefix('author')->middleware(['auth','author'])->group(function () {
    Route::get('/dashboard', [AuthorDashboardController::class, 'index'])->name('author.dashboard');

    // Manage author's own posts
    Route::get('/posts', [AuthorPostController::class, 'index'])->name('author.posts.index');
    Route::post('/posts', [AuthorPostController::class, 'store'])->name('author.posts.store');
    Route::put('/posts/{id}', [AuthorPostController::class, 'update'])->name('author.posts.update');
    Route::delete('/posts/{id}', [AuthorPostController::class, 'destroy'])->name('author.posts.destroy');
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

