<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Livewire\Calendar;
use App\Livewire\CalendarEvents;
use App\Livewire\Dashboard;
use App\Livewire\Home;
use App\Livewire\MoodPrompts;

// Public pages
Route::get('/', Home::class)->name('home');

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
    Route::get('/mood-prompts', MoodPrompts::class)->name('mood.prompts');
    Route::post('/logout', [GoogleAuthController::class, 'logout'])->name('logout');
});

// Admin redirect - redirects to login if not authenticated, or to admin dashboard if authenticated
Route::get('/admin', function () {
    return redirect('/admin/login');
})->name('admin');

// Include route files
require __DIR__ . '/admin.php';
require __DIR__ . '/auth.php';
