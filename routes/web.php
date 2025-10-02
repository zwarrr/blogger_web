<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\UserDashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Public blog routes (user can only view list and detail)
Route::get('/posts', [UserDashboardController::class, 'views'])->name('user.views');
Route::get('/posts/{id}', [UserDashboardController::class, 'detail'])->name('user.detail');

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
});

// No user login/registration; users only view public pages

