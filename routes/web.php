<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;
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

// Public pages
Route::get('/', Home::class)->name('home');

// Sharing invite acceptance (public route)
Route::get('/accept-invite/{token}', AcceptInvite::class)->name('sharing.accept-invite');

// Google OAuth routes
Route::prefix('auth/google')->group(function () {
    Route::get('redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
    Route::get('sync-calendar', [GoogleAuthController::class, 'syncCalendar'])->name('auth.google.sync');
    Route::post('disconnect-calendar', [GoogleAuthController::class, 'disconnectCalendar'])->middleware('auth')->name('auth.google.disconnect');
});

// Protected user routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/calendar', Calendar::class)->name('calendar');
    Route::get('/calendar-events', CalendarEvents::class)->name('calendar.events');
    Route::get('/calendar-settings', \App\Livewire\CalendarSettings::class)->name('calendar.settings');
    Route::get('/mood-prompts', MoodPrompts::class)->name('mood.prompts');
    Route::get('/mood-history', \App\Livewire\MoodHistory::class)->name('mood.history');
    Route::get('/reports', Reports::class)->name('reports');
    Route::get('/ai-insights', AIInsights::class)->name('ai.insights');
    Route::get('/sharing-settings', SharingSettings::class)->name('sharing.settings');
    Route::get('/shared-with-me', SharedWithMe::class)->name('shared.with.me');
    Route::get('/settings', Settings::class)->name('settings');
    Route::post('/logout', [GoogleAuthController::class, 'logout'])->name('logout');
});

// Admin redirect - redirects to login if not authenticated, or to admin dashboard if authenticated
Route::get('/admin', function () {
    return redirect('/admin/login');
})->name('admin');

// Include route files
require __DIR__ . '/admin.php';
require __DIR__ . '/auth.php';
