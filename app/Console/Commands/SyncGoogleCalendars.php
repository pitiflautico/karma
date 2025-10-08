<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\GoogleCalendarService;
use Illuminate\Console\Command;

class SyncGoogleCalendars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:sync-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Google Calendar events for all users with calendar sync enabled';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Google Calendar sync for all users...');

        // Get all users with calendar sync enabled
        $users = User::where('calendar_sync_enabled', true)
            ->whereNotNull('google_calendar_token')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No users with calendar sync enabled.');
            return Command::SUCCESS;
        }

        $service = new GoogleCalendarService();
        $successCount = 0;
        $errorCount = 0;

        foreach ($users as $user) {
            try {
                $this->info("Syncing calendar for user: {$user->name} ({$user->email})");

                $events = $service->syncEvents($user, 50); // Sync up to 50 upcoming events

                $this->info("  - Synced " . count($events) . " events");
                $successCount++;
            } catch (\Exception $e) {
                $this->error("  - Error syncing for {$user->name}: " . $e->getMessage());
                \Log::error('Calendar sync error for user ' . $user->id, [
                    'user' => $user->email,
                    'error' => $e->getMessage()
                ]);
                $errorCount++;
            }
        }

        $this->info('');
        $this->info("Sync completed:");
        $this->info("  - Successful: {$successCount}");
        $this->info("  - Errors: {$errorCount}");

        return Command::SUCCESS;
    }
}
