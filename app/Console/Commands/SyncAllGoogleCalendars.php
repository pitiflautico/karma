<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\GoogleCalendarService;
use Illuminate\Console\Command;

class SyncAllGoogleCalendars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:sync-all
                          {--user= : Only sync for specific user ID}
                          {--limit=50 : Maximum events to sync per user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually sync Google Calendar for all users or a specific user';

    protected GoogleCalendarService $calendarService;

    /**
     * Create a new command instance.
     */
    public function __construct(GoogleCalendarService $calendarService)
    {
        parent::__construct();
        $this->calendarService = $calendarService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user');
        $limit = (int) $this->option('limit');

        $this->info('🔄 Starting Google Calendar synchronization...');

        if ($userId) {
            // Sync specific user
            $user = User::find($userId);

            if (!$user) {
                $this->error("❌ User with ID {$userId} not found");
                return Command::FAILURE;
            }

            $this->info("👤 Syncing calendar for user: {$user->name} ({$user->email})");
            $this->syncUser($user, $limit);
        } else {
            // Sync all users with calendar sync enabled
            $users = User::where('calendar_sync_enabled', true)
                ->whereNotNull('google_calendar_token')
                ->get();

            if ($users->isEmpty()) {
                $this->warn('⚠️  No users found with calendar sync enabled');
                return Command::SUCCESS;
            }

            $this->info("📋 Found {$users->count()} user(s) to sync");

            $synced = 0;
            $failed = 0;

            foreach ($users as $user) {
                $this->line("👤 Syncing: {$user->name} ({$user->email})");

                try {
                    $this->syncUser($user, $limit);
                    $synced++;
                } catch (\Exception $e) {
                    $this->error("   ❌ Failed: " . $e->getMessage());
                    $this->error("   Stack trace: " . $e->getTraceAsString());
                    \Log::error('Calendar sync error', ['exception' => $e]);
                    $failed++;
                }

                // Small delay to avoid API rate limits
                usleep(500000); // 0.5 seconds
            }

            // Summary
            $this->newLine();
            $this->info('📊 Summary:');
            $this->line("   ✅ Synced: {$synced}");
            if ($failed > 0) {
                $this->error("   ❌ Failed: {$failed}");
            }
        }

        $this->newLine();
        $this->info('✅ Synchronization complete!');

        return Command::SUCCESS;
    }

    /**
     * Sync calendar for a specific user
     */
    private function syncUser(User $user, int $limit): void
    {
        $events = $this->calendarService->syncEvents($user, $limit);

        $count = count($events);
        $this->line("   📅 Synced {$count} event(s)");

        if (!empty($events)) {
            $this->line("   ⏰ Last sync: " . $user->fresh()->last_calendar_sync_at);
        }
    }
}
