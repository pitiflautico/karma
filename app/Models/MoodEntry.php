<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoodEntry extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'calendar_event_id',
        'group_id',
        'mood_score',
        'note',
        'is_manual',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'mood_score' => 'integer',
        'is_manual' => 'boolean',
    ];

    /**
     * Get the user that owns the mood entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the calendar event associated with this mood entry.
     */
    public function calendarEvent(): BelongsTo
    {
        return $this->belongsTo(CalendarEvent::class);
    }

    /**
     * Get the group this mood entry belongs to (if any).
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the mood category based on score.
     */
    public function getMoodCategoryAttribute(): string
    {
        return match (true) {
            $this->mood_score <= 3 => 'low',
            $this->mood_score <= 6 => 'medium',
            $this->mood_score <= 8 => 'good',
            default => 'excellent',
        };
    }
}
