<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('expiration:check')->dailyAt('09:00');
Schedule::command('events:check-mood-prompts')->everyThirtyMinutes();
Schedule::command('calendar:sync-all')->hourly();
