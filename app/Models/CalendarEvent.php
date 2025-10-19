<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CalendarEvent extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'google_event_id',
        'title',
        'description',
        'location',
        'start_time',
        'end_time',
        'event_type',
        'is_all_day',
        'reminder_sent',
        'reminder_sent_at',
        'mood_entry_id',
        'mood_prompted',
        'prompted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_all_day' => 'boolean',
        'reminder_sent' => 'boolean',
        'reminder_sent_at' => 'datetime',
        'mood_prompted' => 'boolean',
        'prompted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the calendar event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the mood entry associated with this calendar event.
     */
    public function moodEntry(): HasOne
    {
        return $this->hasOne(MoodEntry::class);
    }

    /**
     * Check if the event has ended.
     */
    public function hasEnded(): bool
    {
        return $this->end_time->isPast();
    }

    /**
     * Check if mood has been logged for this event.
     */
    public function hasMoodLogged(): bool
    {
        return $this->moodEntry()->exists();
    }
}
