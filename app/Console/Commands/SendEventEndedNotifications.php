<?php

namespace App\Console\Commands;

use App\Models\CalendarEvent;
use App\Models\User;
use App\Services\ExpoPushNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEventEndedNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-event-ended
                          {--dry-run : Run without actually sending notifications}
                          {--minutes=15 : Look for events ended in the last N minutes (default: 15)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send push notifications for calendar events that have just ended';

    protected ExpoPushNotificationService $pushService;

    /**
     * Create a new command instance.
     */
    public function __construct(ExpoPushNotificationService $pushService)
    {
        parent::__construct();
        $this->pushService = $pushService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $minutesAgo = (int) $this->option('minutes');

        $this->info("🔍 Looking for events that ended in the last {$minutesAgo} minutes...");

        if ($isDryRun) {
            $this->warn('🧪 DRY RUN MODE - No notifications will be sent');
        }

        // Get current time and the time window
        $now = Carbon::now();
        $windowStart = $now->copy()->subMinutes($minutesAgo);

        $this->line("Time window: {$windowStart->format('Y-m-d H:i:s')} to {$now->format('Y-m-d H:i:s')}");

        // Find events that:
        // 1. Ended in the last N minutes
        // 2. Don't have a reminder sent yet
        // 3. Don't have a mood entry yet
        // 4. User has calendar sync enabled
        $events = CalendarEvent::whereBetween('end_time', [$windowStart, $now])
            ->where('reminder_sent', false)
            ->whereNull('mood_entry_id')
            ->whereHas('user', function ($query) {
                $query->where('calendar_sync_enabled', true)
                      ->whereNotNull('push_token')
                      ->where('push_enabled', true);
            })
            ->with('user')
            ->get();

        if ($events->isEmpty()) {
            $this->info('✅ No events found that need notifications');
            return Command::SUCCESS;
        }

        $this->info("📋 Found {$events->count()} event(s) to notify:");

        $sent = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($events as $event) {
            $user = $event->user;

            // Check if we should respect quiet hours
            if ($this->isQuietHours($user)) {
                $this->line("⏰ [{$event->id}] Skipping \"{$event->title}\" - User in quiet hours");
                $skipped++;
                continue;
            }

            $this->line("📤 [{$event->id}] \"{$event->title}\" for {$user->name} ({$user->email})");
            $this->line("   Ended: {$event->end_time->format('Y-m-d H:i:s')}");

            if ($isDryRun) {
                $this->line("   [DRY RUN] Would send to: " . substr($user->push_token, 0, 30) . '...');
                $sent++;
                continue;
            }

            // Send the notification
            $result = $this->pushService->sendEventEndedNotification(
                pushToken: $user->push_token,
                eventTitle: $event->title,
                eventId: $event->id
            );

            if ($result['success']) {
                // Mark as sent
                $event->update([
                    'reminder_sent' => true,
                    'reminder_sent_at' => now(),
                ]);

                $this->line("   ✅ Notification sent successfully");
                $sent++;
            } else {
                $this->error("   ❌ Failed: " . ($result['error'] ?? 'Unknown error'));
                $failed++;

                // Log the failure
                \Log::warning('Failed to send event ended notification', [
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'error' => $result['error'] ?? 'Unknown',
                ]);

                // If token is invalid, disable push for user
                if (isset($result['error']) && str_contains($result['error'], 'DeviceNotRegistered')) {
                    $user->update(['push_enabled' => false]);
                    $this->warn("   ⚠️  Disabled push notifications for user (invalid token)");
                }
            }
        }

        // Summary
        $this->newLine();
        $this->info('📊 Summary:');
        $this->line("   ✅ Sent: {$sent}");

        if ($failed > 0) {
            $this->error("   ❌ Failed: {$failed}");
        }

        if ($skipped > 0) {
            $this->line("   ⏰ Skipped (quiet hours): {$skipped}");
        }

        return Command::SUCCESS;
    }

    /**
     * Check if user is in quiet hours
     */
    private function isQuietHours(User $user): bool
    {
        if (!$user->quiet_hours_start || !$user->quiet_hours_end) {
            return false;
        }

        $now = Carbon::now();
        $start = Carbon::parse($user->quiet_hours_start);
        $end = Carbon::parse($user->quiet_hours_end);

        // Handle quiet hours that span midnight
        if ($end->lessThan($start)) {
            return $now->greaterThanOrEqualTo($start) || $now->lessThanOrEqualTo($end);
        }

        return $now->between($start, $end);
    }
}
