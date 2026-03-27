<?php

use App\Http\Controllers\Web\AnalyticsWebController;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\CampaignWebController;
use App\Http\Controllers\Web\ContactWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\EmailTemplateWebController;
use App\Http\Controllers\Web\LeadFormWebController;
use App\Http\Controllers\Web\SegmentWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Public pages ---
Route::get('/', fn() => redirect()->route('login'))->name('home');

// --- Public lead form embed & submit ---
Route::get('/lead-forms/{slug}/embed', [LeadFormWebController::class, 'embed'])->name('lead-forms.embed');
Route::post('/lead-forms/{slug}/submit', [LeadFormWebController::class, 'publicSubmit'])->name('lead-forms.public-submit');

// --- Auth pages (guests only) ---
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthWebController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthWebController::class, 'login']);
    Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthWebController::class, 'register']);

    // Google OAuth
    Route::get('/auth/google',          [AuthWebController::class, 'googleRedirect'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthWebController::class, 'googleCallback'])->name('auth.google.callback');
});

// --- Authenticated routes ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Campaigns
    Route::get('/campaigns/calendar', [CampaignWebController::class, 'calendar'])->name('campaigns.calendar');
    Route::resource('/campaigns', CampaignWebController::class)->names('campaigns');

    // Contacts
    Route::get('/contacts/import',  [ContactWebController::class, 'importForm'])->name('contacts.import-form');
    Route::post('/contacts/import', [ContactWebController::class, 'import'])->name('contacts.import');
    Route::resource('/contacts', ContactWebController::class)->names('contacts');

    // Segments
    Route::post('/segments/{segment}/export', [SegmentWebController::class, 'export'])->name('segments.export');
    Route::resource('/segments', SegmentWebController::class)->names('segments');

    // Email Templates
    Route::resource('/email-templates', EmailTemplateWebController::class)->names('email-templates');

    // Analytics
    Route::get('/analytics', [AnalyticsWebController::class, 'index'])->name('analytics.index');

    // Lead Forms
    Route::post('/lead-forms/{leadForm}/export-submissions', [LeadFormWebController::class, 'exportSubmissions'])->name('lead-forms.export');
    Route::resource('/lead-forms', LeadFormWebController::class)->names('lead-forms');
});
