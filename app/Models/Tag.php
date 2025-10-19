<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'emoji',
        'category',
        'mood_associations',
        'is_custom',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'mood_associations' => 'array',
        'is_custom' => 'boolean',
    ];

    /**
     * Get the user that created this custom tag.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The mood entries that have this tag.
     */
    public function moodEntries(): BelongsToMany
    {
        return $this->belongsToMany(MoodEntry::class, 'mood_entry_tag');
    }

    /**
     * Scope to get only system tags (predefined).
     */
    public function scopeSystem($query)
    {
        return $query->where('is_custom', false)->whereNull('user_id');
    }

    /**
     * Scope to get tags for a specific mood score.
     */
    public function scopeForMoodScore($query, int $moodScore)
    {
        return $query->where(function ($q) use ($moodScore) {
            $q->whereJsonContains('mood_associations', $moodScore)
              ->orWhereNull('mood_associations');
        });
    }

    /**
     * Scope to get user's custom tags.
     */
    public function scopeCustomForUser($query, $userId)
    {
        return $query->where('is_custom', true)->where('user_id', $userId);
    }
}
