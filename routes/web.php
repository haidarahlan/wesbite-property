<?php

use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;

// Website Publik (Fase 2C)
Route::get('/', [\App\Http\Controllers\PublicController::class, 'home'])->name('public.home');
Route::get('/properti', [\App\Http\Controllers\PublicController::class, 'properties'])->name('public.properties');
Route::get('/properti/{slug}', [\App\Http\Controllers\PublicController::class, 'propertyDetail'])->name('public.property.detail');
Route::get('/sitemap.xml', [\App\Http\Controllers\PublicController::class, 'sitemap'])->name('public.sitemap');

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
    // CMS Company Profile (Fase 2B)
    Route::get('/cms', [\App\Http\Controllers\Admin\CmsController::class, 'index'])->name('cms.index');
    Route::post('/cms/settings', [\App\Http\Controllers\Admin\CmsController::class, 'updateSettings'])->name('cms.settings.update');
    Route::put('/cms/sections/{sectionKey}', [\App\Http\Controllers\Admin\CmsController::class, 'updateSection'])->name('cms.section.update');
    Route::post('/cms/services', [\App\Http\Controllers\Admin\CmsController::class, 'storeService'])->name('cms.services.store');
    Route::delete('/cms/services/{service}', [\App\Http\Controllers\Admin\CmsController::class, 'deleteService'])->name('cms.services.destroy');
    Route::post('/cms/testimonials', [\App\Http\Controllers\Admin\CmsController::class, 'storeTestimonial'])->name('cms.testimonials.store');
    Route::delete('/cms/testimonials/{testimonial}', [\App\Http\Controllers\Admin\CmsController::class, 'deleteTestimonial'])->name('cms.testimonials.destroy');
    Route::post('/cms/faqs', [\App\Http\Controllers\Admin\CmsController::class, 'storeFaq'])->name('cms.faqs.store');
    Route::delete('/cms/faqs/{faq}', [\App\Http\Controllers\Admin\CmsController::class, 'deleteFaq'])->name('cms.faqs.destroy');
    Route::post('/cms/teams', [\App\Http\Controllers\Admin\CmsController::class, 'storeTeam'])->name('cms.teams.store');
    Route::delete('/cms/teams/{teamMember}', [\App\Http\Controllers\Admin\CmsController::class, 'deleteTeam'])->name('cms.teams.destroy');
});

Route::redirect('/dashboard', '/admin/units')->name('dashboard');
