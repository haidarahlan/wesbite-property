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
    // WhatsApp Simulator & Rules (Fase 3A & 3B)
    Route::get('/whatsapp/simulator', [\App\Http\Controllers\Admin\WhatsAppSimulatorController::class, 'index'])->name('whatsapp.simulator');
    Route::post('/whatsapp/simulator/inbound', [\App\Http\Controllers\Admin\WhatsAppSimulatorController::class, 'simulateInbound'])->name('whatsapp.simulate-inbound');
    Route::post('/whatsapp/simulator/send', [\App\Http\Controllers\Admin\WhatsAppSimulatorController::class, 'testSend'])->name('whatsapp.test-send');
    Route::get('/whatsapp/rules', [\App\Http\Controllers\Admin\WhatsAppRuleController::class, 'index'])->name('whatsapp.rules.index');
    Route::post('/whatsapp/rules', [\App\Http\Controllers\Admin\WhatsAppRuleController::class, 'storeRule'])->name('whatsapp.rules.store');
    Route::delete('/whatsapp/rules/{rule}', [\App\Http\Controllers\Admin\WhatsAppRuleController::class, 'destroyRule'])->name('whatsapp.rules.destroy');
    Route::patch('/whatsapp/rules/{rule}/toggle', [\App\Http\Controllers\Admin\WhatsAppRuleController::class, 'toggleRule'])->name('whatsapp.rules.toggle');
    Route::post('/whatsapp/templates', [\App\Http\Controllers\Admin\WhatsAppRuleController::class, 'storeFaqTemplate'])->name('whatsapp.templates.store');
    Route::delete('/whatsapp/templates/{template}', [\App\Http\Controllers\Admin\WhatsAppRuleController::class, 'destroyFaqTemplate'])->name('whatsapp.templates.destroy');

    // Manajemen Leads & Chat (Fase 3C)
    Route::get('/leads', [\App\Http\Controllers\Admin\LeadController::class, 'index'])->name('leads.index');
    Route::post('/leads', [\App\Http\Controllers\Admin\LeadController::class, 'store'])->name('leads.store');
    Route::get('/leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'show'])->name('leads.show');
    Route::put('/leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'update'])->name('leads.update');
    Route::post('/leads/{lead}/reply', [\App\Http\Controllers\Admin\LeadController::class, 'sendReply'])->name('leads.reply');
    Route::delete('/leads/{lead}', [\App\Http\Controllers\Admin\LeadController::class, 'destroy'])->name('leads.destroy');

    // Survei Lapangan & Pengingat H-1 (Fase 3D)
    Route::get('/surveys', [\App\Http\Controllers\Admin\SurveyController::class, 'index'])->name('surveys.index');
    Route::post('/surveys', [\App\Http\Controllers\Admin\SurveyController::class, 'store'])->name('surveys.store');
    Route::put('/surveys/{survey}', [\App\Http\Controllers\Admin\SurveyController::class, 'update'])->name('surveys.update');
    Route::post('/surveys/{survey}/manual-reminder', [\App\Http\Controllers\Admin\SurveyController::class, 'sendManualReminder'])->name('surveys.manual-reminder');
    Route::delete('/surveys/{survey}', [\App\Http\Controllers\Admin\SurveyController::class, 'destroy'])->name('surveys.destroy');
});

// Webhook WhatsApp (Terbuka untuk provider eksternal / CSRF exempt)
Route::post('/webhook/whatsapp', [\App\Http\Controllers\Api\WhatsAppWebhookController::class, 'handle'])->name('webhook.whatsapp');

Route::redirect('/dashboard', '/admin/units')->name('dashboard');
