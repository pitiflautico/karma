<?php

namespace App\Console\Commands;

use App\Models\CalendarEvent;
use App\Models\MoodPrompt;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckPastEventsForMoodPrompts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:check-mood-prompts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for past calendar events and create mood prompts for users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for past events that need mood prompts...');

        // Find events that:
        // 1. Have ended (end_time < now)
        // 2. Haven't been prompted yet (mood_prompted = false)
        // 3. Ended within the last 24 hours (to avoid prompting for very old events)
        $pastEvents = CalendarEvent::where('end_time', '<', now())
            ->where('end_time', '>=', now()->subHours(24))
            ->where('mood_prompted', false)
            ->with('user')
            ->get();

        if ($pastEvents->isEmpty()) {
            $this->info('No events found that need mood prompts.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($pastEvents as $event) {
            // Create mood prompt for this event
            MoodPrompt::create([
                'user_id' => $event->user_id,
                'calendar_event_id' => $event->id,
                'prompt_text' => "How did you feel during and after '{$event->title}'?",
                'event_title' => $event->title,
                'event_start_time' => $event->start_time,
                'event_end_time' => $event->end_time,
                'is_completed' => false,
            ]);

            // Mark event as prompted
            $event->update([
                'mood_prompted' => true,
                'prompted_at' => now(),
            ]);

            $count++;
            $this->info("Created mood prompt for event: {$event->title} (User: {$event->user->name})");
        }

        $this->info("Successfully created {$count} mood prompts.");
        return Command::SUCCESS;
    }
}
