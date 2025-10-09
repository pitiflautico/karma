<?php

namespace App\Console\Commands;

use App\Models\CalendarEvent;
use App\Models\MoodPrompt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMoodPromptsCommand extends Command
{
    protected $signature = 'prompts:generate {--user-id= : Generate prompts for specific user}';
    protected $description = 'Generate mood prompts for ended calendar events that don\'t have mood entries yet';

    public function handle()
    {
        $this->info('🔍 Generating mood prompts for ended events...');

        $usersQuery = User::where('calendar_sync_enabled', true);

        if ($this->option('user-id')) {
            $usersQuery->where('id', $this->option('user-id'));
        }

        $users = $usersQuery->get();

        if ($users->isEmpty()) {
            $this->warn('No users with calendar sync enabled found.');
            return 0;
        }

        $totalPrompts = 0;

        foreach ($users as $user) {
            $this->info("Processing user: {$user->name} ({$user->email})");

            // Get events that have ended but don't have mood entries or prompts
            $events = CalendarEvent::where('user_id', $user->id)
                ->where('end_time', '<', now())
                ->where('end_time', '>=', now()->subDays(7)) // Only last 7 days
                ->whereDoesntHave('moodEntry')
                ->get();

            $promptsCreated = 0;

            foreach ($events as $event) {
                // Check if prompt already exists for this event
                $existingPrompt = MoodPrompt::where('user_id', $user->id)
                    ->where('calendar_event_id', $event->id)
                    ->exists();

                if ($existingPrompt) {
                    continue;
                }

                // Check quiet hours
                if ($this->isInQuietHours($user, now())) {
                    $this->line("  ⏰ Skipping prompt (quiet hours): {$event->title}");
                    continue;
                }

                // Create mood prompt
                MoodPrompt::create([
                    'user_id' => $user->id,
                    'calendar_event_id' => $event->id,
                    'event_title' => $event->title,
                    'event_start_time' => $event->start_time,
                    'event_end_time' => $event->end_time,
                    'is_completed' => false,
                ]);

                $promptsCreated++;
                $this->line("  ✅ Created prompt for: {$event->title}");
            }

            $this->info("  Created {$promptsCreated} prompts for {$user->name}");
            $totalPrompts += $promptsCreated;
        }

        $this->newLine();
        $this->info("✨ Total prompts created: {$totalPrompts}");

        return 0;
    }

    private function isInQuietHours(User $user, Carbon $time): bool
    {
        if (!$user->quiet_hours_start || !$user->quiet_hours_end) {
            return false;
        }

        $quietStart = Carbon::parse($user->quiet_hours_start);
        $quietEnd = Carbon::parse($user->quiet_hours_end);
        $currentTime = Carbon::parse($time->format('H:i'));

        // Handle quiet hours that span midnight
        if ($quietStart->greaterThan($quietEnd)) {
            return $currentTime->greaterThanOrEqualTo($quietStart) ||
                   $currentTime->lessThanOrEqualTo($quietEnd);
        }

        return $currentTime->between($quietStart, $quietEnd);
    }
}
