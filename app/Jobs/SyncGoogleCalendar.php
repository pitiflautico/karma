<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\GoogleCalendarService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncGoogleCalendar implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleCalendarService $service): void
    {
        if (!$this->user->calendar_sync_enabled || !$this->user->google_calendar_token) {
            \Log::info("Skipping calendar sync for user {$this->user->id} - sync not enabled or no token");
            return;
        }

        try {
            $events = $service->syncEvents($this->user, 50);

            \Log::info("Calendar synced successfully for user {$this->user->id}", [
                'events_count' => count($events),
                'user_email' => $this->user->email,
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to sync calendar for user {$this->user->id}", [
                'user_email' => $this->user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw to mark job as failed and retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error("Calendar sync job failed after all retries for user {$this->user->id}", [
            'user_email' => $this->user->email,
            'error' => $exception->getMessage(),
        ]);

        // TODO: Send notification to user about sync failure
    }
}
