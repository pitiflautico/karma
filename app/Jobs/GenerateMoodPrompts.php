<?php

namespace App\Jobs;

use App\Models\CalendarEvent;
use App\Models\MoodPrompt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMoodPrompts implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    /**
     * Execute the job - check all users for ended events that need prompts.
     */
    public function handle(): void
    {
        $users = User::where('calendar_sync_enabled', true)->get();

        $totalPromptsCreated = 0;

        foreach ($users as $user) {
            $promptsCreated = $this->generatePromptsForUser($user);
            $totalPromptsCreated += $promptsCreated;
        }

        \Log::info("GenerateMoodPrompts job completed", [
            'total_prompts_created' => $totalPromptsCreated,
        ]);
    }

    /**
     * Generate mood prompts for a specific user.
     */
    private function generatePromptsForUser(User $user): int
    {
        // Get events that have ended but don't have mood entries
        $events = CalendarEvent::where('user_id', $user->id)
            ->where('end_time', '<', now())
            ->where('end_time', '>=', now()->subDays(7)) // Only last 7 days
            ->whereDoesntHave('moodEntry')
            ->get();

        $promptsCreated = 0;

        foreach ($events as $event) {
            // Check if prompt already exists
            $existingPrompt = MoodPrompt::where('user_id', $user->id)
                ->where('calendar_event_id', $event->id)
                ->exists();

            if ($existingPrompt) {
                continue;
            }

            // Check quiet hours
            if ($this->isInQuietHours($user, now())) {
                \Log::debug("Skipping prompt generation due to quiet hours", [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                ]);
                continue;
            }

            // Check for back-to-back events (smart timing)
            if ($this->hasBackToBackEvent($event)) {
                \Log::debug("Delaying prompt due to back-to-back event", [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                ]);
                continue;
            }

            // Create mood prompt
            $moodPrompt = MoodPrompt::create([
                'user_id' => $user->id,
                'calendar_event_id' => $event->id,
                'event_title' => $event->title,
                'event_start_time' => $event->start_time,
                'event_end_time' => $event->end_time,
                'is_completed' => false,
            ]);

            // Send notification to user
            $user->notify(new \App\Notifications\MoodReminderNotification($moodPrompt));

            $promptsCreated++;
        }

        if ($promptsCreated > 0) {
            \Log::info("Created mood prompts for user", [
                'user_id' => $user->id,
                'prompts_created' => $promptsCreated,
            ]);
        }

        return $promptsCreated;
    }

    /**
     * Check if current time is within user's quiet hours.
     */
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

    /**
     * Check if user has a back-to-back event starting soon.
     */
    private function hasBackToBackEvent(CalendarEvent $event): bool
    {
        // Check if there's another event starting within 5 minutes of this one ending
        $nextEvent = CalendarEvent::where('user_id', $event->user_id)
            ->where('start_time', '>', $event->end_time)
            ->where('start_time', '<', $event->end_time->copy()->addMinutes(5))
            ->first();

        return $nextEvent !== null;
    }
}
