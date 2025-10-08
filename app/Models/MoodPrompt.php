<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoodPrompt extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'calendar_event_id',
        'mood_entry_id',
        'prompt_text',
        'event_title',
        'event_start_time',
        'event_end_time',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'event_start_time' => 'datetime',
        'event_end_time' => 'datetime',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the mood prompt
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the calendar event that triggered this prompt
     */
    public function calendarEvent(): BelongsTo
    {
        return $this->belongsTo(CalendarEvent::class);
    }

    /**
     * Get the mood entry created in response to this prompt
     */
    public function moodEntry(): BelongsTo
    {
        return $this->belongsTo(MoodEntry::class);
    }
}
