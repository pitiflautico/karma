<?php

namespace App\Console\Commands;

use App\Services\ExpoPushNotificationService;
use Illuminate\Console\Command;

class TestPushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test
                          {pushToken : The Expo push token to send to}
                          {--delay=10 : Delay in seconds before sending (default: 10)}
                          {--event-id=test-123 : Event ID for testing}
                          {--event-title=Test Event : Event title for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test event_mood push notification after a delay';

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
        $pushToken = $this->argument('pushToken');
        $delay = (int) $this->option('delay');
        $eventId = $this->option('event-id');
        $eventTitle = $this->option('event-title');

        $this->info("🧪 Test Push Notification");
        $this->line("   Token: " . substr($pushToken, 0, 30) . '...');
        $this->line("   Delay: {$delay} seconds");
        $this->line("   Event ID: {$eventId}");
        $this->line("   Event Title: {$eventTitle}");
        $this->newLine();

        // Countdown
        $this->info("⏱️  Waiting {$delay} seconds before sending...");
        for ($i = $delay; $i > 0; $i--) {
            $this->line("   {$i}...");
            sleep(1);
        }

        $this->newLine();
        $this->info("📤 Sending notification...");

        // Send the notification
        $result = $this->pushService->sendEventEndedNotification(
            pushToken: $pushToken,
            eventTitle: $eventTitle,
            eventId: $eventId
        );

        $this->newLine();
        if ($result['success']) {
            $this->info("✅ Notification sent successfully!");
            $this->line("   Title: How did it go?");
            $this->line("   Body: Rate your experience: {$eventTitle}");
            $this->line("   Type: event_mood");
            $this->line("   Event ID: {$eventId}");
        } else {
            $this->error("❌ Failed to send notification");
            $this->error("   Error: " . ($result['error'] ?? 'Unknown error'));
        }

        return $result['success'] ? Command::SUCCESS : Command::FAILURE;
    }
}
