<?php

use App\Jobs\GenerateMoodPrompts;
use App\Jobs\SyncGoogleCalendar;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Old command-based scheduling (keeping for backwards compatibility)
Schedule::command('expiration:check')->dailyAt('09:00');

// New job-based scheduling (recommended)
// Sync Google Calendar every 15 minutes for all users
Schedule::call(function () {
    $users = User::where('calendar_sync_enabled', true)
        ->whereNotNull('google_calendar_token')
        ->get();

    foreach ($users as $user) {
        SyncGoogleCalendar::dispatch($user);
    }
})->everyFifteenMinutes()->name('sync-calendars');

// Generate mood prompts every 30 minutes
Schedule::job(new GenerateMoodPrompts)->everyThirtyMinutes()->name('generate-mood-prompts');
