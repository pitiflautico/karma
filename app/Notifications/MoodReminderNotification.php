<?php

namespace App\Notifications;

use App\Models\MoodPrompt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MoodReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $moodPrompt;

    /**
     * Create a new notification instance.
     */
    public function __construct(MoodPrompt $moodPrompt)
    {
        $this->moodPrompt = $moodPrompt;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('How did you feel after ' . $this->moodPrompt->event_title . '?')
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('Your event "' . $this->moodPrompt->event_title . '" just ended.')
            ->line('Take a moment to reflect on how you felt during this event.')
            ->action('Log Your Mood', route('dashboard'))
            ->line('Tracking your emotions helps you understand patterns and improve your wellbeing.');
    }

    /**
     * Get the array representation of the notification (for database).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'mood_prompt_id' => $this->moodPrompt->id,
            'event_title' => $this->moodPrompt->event_title,
            'event_end_time' => $this->moodPrompt->event_end_time,
            'message' => 'How did you feel after "' . $this->moodPrompt->event_title . '"?',
            'action_url' => route('dashboard'),
        ];
    }
}
