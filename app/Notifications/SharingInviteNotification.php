<?php

namespace App\Notifications;

use App\Models\SharingInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SharingInviteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $invite;

    /**
     * Create a new notification instance.
     */
    public function __construct(SharingInvite $invite)
    {
        $this->invite = $invite;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $acceptUrl = route('sharing.accept-invite', ['token' => $this->invite->token]);

        $permissions = [];
        if ($this->invite->can_view_moods) $permissions[] = 'Mood scores';
        if ($this->invite->can_view_notes) $permissions[] = 'Notes';
        if ($this->invite->can_view_selfies) $permissions[] = 'Emotional selfies';

        $permissionsText = empty($permissions)
            ? 'No permissions granted'
            : implode(', ', $permissions);

        return (new MailMessage)
            ->subject($this->invite->sender->name . ' wants to share their emotional wellbeing data with you')
            ->greeting('Hello!')
            ->line($this->invite->sender->name . ' (' . $this->invite->sender->email . ') has invited you to view their emotional wellbeing data on Karma.')
            ->line('**They have granted you permission to view:**')
            ->line($permissionsText)
            ->action('Accept Invitation', $acceptUrl)
            ->line('This invitation will expire ' . $this->invite->expires_at->diffForHumans() . '.')
            ->line('If you don\'t want to accept this invitation, you can simply ignore this email.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invite_id' => $this->invite->id,
            'sender_name' => $this->invite->sender->name,
            'sender_email' => $this->invite->sender->email,
        ];
    }
}
