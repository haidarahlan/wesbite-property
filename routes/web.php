<?php

use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;

// Website Publik (Fase 2C)
Route::get('/', [\App\Http\Controllers\PublicController::class, 'home'])->name('public.home');
Route::get('/properti', [\App\Http\Controllers\PublicController::class, 'properties'])->name('public.properties');
Route::get('/properti/{slug}', [\App\Http\Controllers\PublicController::class, 'propertyDetail'])->name('public.property.detail');
Route::post('/properti/{slug}/minat', [\App\Http\Controllers\PublicController::class, 'submitInterest'])->name('user.properties.interest');
Route::get('/sitemap.xml', [\App\Http\Controllers\PublicController::class, 'sitemap'])->name('public.sitemap');

// Halaman Publik Konfirmasi Ketersediaan Developer via Token (Fase 4B)
Route::get('/cek-ketersediaan/{token}', [\App\Http\Controllers\DeveloperConfirmationController::class, 'show'])->name('public.developer.confirm');
Route::post('/cek-ketersediaan/{token}', [\App\Http\Controllers\DeveloperConfirmationController::class, 'submit'])->name('public.developer.confirm.submit');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin & Superadmin Dashboard (Dilindungi middleware auth)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('units', UnitController::class);
    Route::delete('units/photos/{photo}', [UnitController::class, 'deletePhoto'])->name('units.photos.destroy');
    Route::delete('units/{id}/force', [UnitController::class, 'forceDelete'])->name('units.force-delete');

    // Manajemen User (Superadmin & Managing Director)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

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

    // Manajemen Leads & Bukti Chat Konsumen (Fase 3)
    Route::get('/leads', [\App\Http\Controllers\Admin\LeadController::class, 'index'])->name('leads.index');
    Route::post('/leads', [\App\Http\Controllers\Admin\LeadController::class, 'store'])->name('leads.store');
    Route::get('/leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'show'])->name('leads.show');
    Route::put('/leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'update'])->name('leads.update');
    Route::post('/leads/{lead}/chat-proof', [\App\Http\Controllers\Admin\LeadController::class, 'uploadChatProof'])->name('leads.chat-proof.store');
    Route::delete('/leads/chat-proof/{proof}', [\App\Http\Controllers\Admin\LeadController::class, 'deleteChatProof'])->name('leads.chat-proof.destroy');
    Route::delete('/leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'destroy'])->name('leads.destroy');

    // Survei Lapangan & Pengingat H-1 (Fase 3D)
    Route::get('/surveys', [\App\Http\Controllers\Admin\SurveyController::class, 'index'])->name('surveys.index');
    Route::post('/surveys', [\App\Http\Controllers\Admin\SurveyController::class, 'store'])->name('surveys.store');
    Route::put('/surveys/{survey}', [\App\Http\Controllers\Admin\SurveyController::class, 'update'])->name('surveys.update');
    Route::delete('/surveys/{survey}', [\App\Http\Controllers\Admin\SurveyController::class, 'destroy'])->name('surveys.destroy');

    // Mitra Developer & Cek Ketersediaan Token (Fase 4A & 4B)
    Route::resource('developers', \App\Http\Controllers\Admin\DeveloperController::class);
    Route::post('/units/{unit}/check-availability', [\App\Http\Controllers\Admin\AvailabilityRequestController::class, 'store'])->name('units.check-availability');

    // Strategi & Target Akuisisi (Fase 5A)
    Route::get('/acquisitions', [\App\Http\Controllers\Admin\AcquisitionController::class, 'index'])->name('acquisitions.index');
    Route::post('/acquisitions/targets', [\App\Http\Controllers\Admin\AcquisitionController::class, 'storeTarget'])->name('acquisitions.targets.store');
    Route::post('/acquisitions/candidates', [\App\Http\Controllers\Admin\AcquisitionController::class, 'storeCandidate'])->name('acquisitions.candidates.store');
    Route::post('/acquisitions/candidates/{candidate}/review', [\App\Http\Controllers\Admin\AcquisitionController::class, 'reviewCandidate'])->name('acquisitions.candidates.review');

    // Executive Dashboard Analitik & Laporan (Fase 5B)
    Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsReportController::class, 'dashboard'])->name('analytics.dashboard');
    Route::get('/reports', [\App\Http\Controllers\Admin\AnalyticsReportController::class, 'reports'])->name('reports.index');
    Route::get('/reports/print', [\App\Http\Controllers\Admin\AnalyticsReportController::class, 'printReport'])->name('reports.print');
    Route::get('/reports/export-leads', [\App\Http\Controllers\Admin\AnalyticsReportController::class, 'exportLeadsCsv'])->name('reports.export.leads');

    // Pengaturan Akun & Profil (Ganti Nama, Email, Password, Foto Profil, Tanda Tangan Digital)
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

Route::redirect('/dashboard', '/admin/units')->name('dashboard');
