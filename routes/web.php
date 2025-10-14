<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Api\AuthController;
use App\Livewire\Calendar;
use App\Livewire\CalendarEvents;
use App\Livewire\Dashboard;
use App\Livewire\Home;
use App\Livewire\MoodPrompts;
use App\Livewire\Reports;
use App\Livewire\SharingSettings;
use App\Livewire\SharedWithMe;
use App\Livewire\AcceptInvite;
use App\Livewire\AIInsights;
use App\Livewire\Settings;
use App\Livewire\Selfies;

// Public pages
Route::get('/', \App\Livewire\Auth\AuthHome::class)->name('home');
Route::get('/sign-in-mail', Home::class)->name('sign-in-mail');
Route::get('/terms', \App\Livewire\Terms::class)->name('terms');
Route::get('/privacy', \App\Livewire\Privacy::class)->name('privacy');

// Test/Debug page (remove in production)
Route::get('/test', \App\Livewire\TestScreen::class)->name('test');

// Sharing invite acceptance (public route)
Route::get('/accept-invite/{token}', AcceptInvite::class)->name('sharing.accept-invite');

// Auth routes
Route::prefix('auth')->group(function () {
    // Mobile app session establishment
    Route::get('session', [AuthController::class, 'sessionFromToken'])->name('auth.session');

    // Google OAuth routes
    Route::prefix('google')->group(function () {
        Route::get('redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google');
        Route::get('callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
        Route::get('sync-calendar', [GoogleAuthController::class, 'syncCalendar'])->name('auth.google.sync');
        Route::post('disconnect-calendar', [GoogleAuthController::class, 'disconnectCalendar'])->middleware('auth')->name('auth.google.disconnect');
    });
});

// Onboarding routes (auth required but no onboarding check)
Route::middleware(['auth'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/', \App\Livewire\Onboarding::class)->name('index');
    Route::get('/step-1', \App\Livewire\Onboarding\Step1::class)->name('step1');
    Route::get('/step-2', \App\Livewire\Onboarding\Step2::class)->name('step2');
    Route::get('/step-3', \App\Livewire\Onboarding\Step3::class)->name('step3');
    Route::get('/step-4', \App\Livewire\Onboarding\Step4::class)->name('step4');
    Route::get('/step-5', \App\Livewire\Onboarding\Step5::class)->name('step5');
    // Route::get('/step-6', \App\Livewire\Onboarding\Step6::class)->name('step6');
    // Route::get('/step-7', \App\Livewire\Onboarding\Step7::class)->name('step7');
    // Route::get('/complete', \App\Livewire\Onboarding\Complete::class)->name('complete');
});

// Protected user routes (require onboarding completion)
Route::middleware(['auth', 'onboarding.completed'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/mood/new', \App\Livewire\MoodCreate::class)->name('mood.new');
    Route::get('/calendar', Calendar::class)->name('calendar');
    Route::get('/calendar-events', CalendarEvents::class)->name('calendar.events');
    Route::get('/calendar-settings', \App\Livewire\CalendarSettings::class)->name('calendar.settings');
    Route::get('/mood-prompts', MoodPrompts::class)->name('mood.prompts');
    Route::get('/mood-history', \App\Livewire\MoodHistory::class)->name('mood.history');
    Route::get('/reports', Reports::class)->name('reports');
    Route::get('/ai-insights', AIInsights::class)->name('ai.insights');
    Route::get('/sharing-settings', SharingSettings::class)->name('sharing.settings');
    Route::get('/shared-with-me', SharedWithMe::class)->name('shared.with.me');
    Route::get('/selfies', Selfies::class)->name('selfies');
    Route::get('/settings', Settings::class)->name('settings');
    Route::post('/logout', [GoogleAuthController::class, 'logout'])->name('logout');
});

// Native app session management
Route::post('/clear-native-token', function () {
    session()->forget('native_app_login');
    session()->forget('native_app_token');
    session()->forget('native_app_debug');
    return response()->json(['success' => true]);
})->name('clear-native-token');

// Admin redirect - redirects to login if not authenticated, or to admin dashboard if authenticated
Route::get('/admin', function () {
    return redirect('/admin/login');
})->name('admin');

// Include route files
require __DIR__ . '/admin.php';
require __DIR__ . '/auth.php';
