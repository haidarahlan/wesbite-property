<?php

use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;

// Website Publik
Route::get('/', function () {
    return view('user.home');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin & Superadmin Dashboard (Dilindungi middleware auth)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('units', UnitController::class);
    Route::delete('units/photos/{photo}', [UnitController::class, 'deletePhoto'])->name('units.photos.destroy');
    Route::delete('units/{id}/force', [UnitController::class, 'forceDelete'])->name('units.force-delete');

    // Manajemen User (Khusus Superadmin)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
});

Route::redirect('/dashboard', '/admin/units')->name('dashboard');
