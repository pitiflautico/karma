<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupEvent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group_id',
        'calendar_event_id',
        'title',
        'description',
        'event_date',
        'created_by',
        'is_custom',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_date' => 'datetime',
        'is_custom' => 'boolean',
    ];

    /**
     * Get the group this event belongs to.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the calendar event (if not custom).
     */
    public function calendarEvent(): BelongsTo
    {
        return $this->belongsTo(CalendarEvent::class);
    }

    /**
     * Get the user who created this event.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all mood ratings for this event.
     */
    public function moods(): HasMany
    {
        return $this->hasMany(GroupEventMood::class);
    }

    /**
     * Get the average mood rating for this event.
     */
    public function getAverageMood(): float
    {
        return $this->moods()->avg('mood_score') ?? 0;
    }

    /**
     * Get the number of ratings for this event.
     */
    public function getRatingCount(): int
    {
        return $this->moods()->count();
    }

    /**
     * Check if a specific user has rated this event.
     */
    public function hasUserRated(User $user): bool
    {
        return $this->moods()->where('user_id', $user->id)->exists();
    }

    /**
     * Get a specific user's rating for this event.
     */
    public function getUserRating(User $user): ?GroupEventMood
    {
        return $this->moods()->where('user_id', $user->id)->first();
    }
}
