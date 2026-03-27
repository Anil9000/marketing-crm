<?php

use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\EmailTemplateController;
use App\Http\Controllers\Api\LeadFormController;
use App\Http\Controllers\Api\SegmentController;
use App\Http\Controllers\Api\TrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - v1
|--------------------------------------------------------------------------
*/

// --- Tracking endpoints (public, no auth) ---
Route::prefix('track')->group(function () {
    Route::get('open/{token}', [TrackingController::class, 'open'])->name('track.open');
    Route::get('click/{token}', [TrackingController::class, 'click'])->name('track.click');
});

// --- Public lead form submission ---
Route::post('v1/lead-forms/{slug}/submit', [LeadFormController::class, 'submit'])
    ->name('api.lead-forms.submit');

Route::prefix('v1')->group(function () {

    // --- Auth ---
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('api.auth.register');
        Route::post('login',    [AuthController::class, 'login'])->name('api.auth.login');
        Route::get('google/redirect', [AuthController::class, 'googleRedirect'])->name('api.auth.google.redirect');
        Route::get('google/callback', [AuthController::class, 'googleCallback'])->name('api.auth.google.callback');

        // Protected auth routes
        Route::middleware('auth:api')->group(function () {
            Route::post('logout',  [AuthController::class, 'logout'])->name('api.auth.logout');
            Route::post('refresh', [AuthController::class, 'refresh'])->name('api.auth.refresh');
            Route::get('me',       [AuthController::class, 'me'])->name('api.auth.me');
        });
    });

    // --- Protected routes ---
    Route::middleware('auth:api')->group(function () {

        // Campaigns
        Route::apiResource('campaigns', CampaignController::class)->names('api.campaigns');
        Route::get('campaigns/{campaign}/stats', [CampaignController::class, 'stats'])->name('api.campaigns.stats');
        Route::post('campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('api.campaigns.send');

        // Segments
        Route::apiResource('segments', SegmentController::class)->names('api.segments');
        Route::get('segments/{segment}/contacts', [SegmentController::class, 'contacts'])->name('api.segments.contacts');
        Route::post('segments/{segment}/export', [SegmentController::class, 'export'])->name('api.segments.export');
        Route::post('segments/preview-count', [SegmentController::class, 'previewCount'])->name('api.segments.preview-count');

        // Contacts
        Route::post('contacts/import', [ContactController::class, 'import'])->name('api.contacts.import');
        Route::apiResource('contacts', ContactController::class)->names('api.contacts');

        // Email Templates
        Route::apiResource('email-templates', EmailTemplateController::class)->names('api.email-templates');

        // Analytics
        Route::prefix('analytics')->name('api.analytics.')->group(function () {
            Route::get('overview',  [AnalyticsController::class, 'overview'])->name('overview');
            Route::get('campaigns', [AnalyticsController::class, 'campaigns'])->name('campaigns');
            Route::get('emails',    [AnalyticsController::class, 'emails'])->name('emails');
        });

        // Lead Forms
        Route::apiResource('lead-forms', LeadFormController::class)->names('api.lead-forms');
        Route::get('lead-forms/{leadForm}/submissions', [LeadFormController::class, 'submissions'])->name('api.lead-forms.submissions');
    });
});
