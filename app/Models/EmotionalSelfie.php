<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EmotionalSelfie extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'mood_entry_id',
        'image_path',
        'filter_type',
        'mood_score_at_capture',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'mood_score_at_capture' => 'integer',
    ];

    /**
     * Get the user that owns the emotional selfie.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the mood entry associated with this selfie.
     */
    public function moodEntry(): BelongsTo
    {
        return $this->belongsTo(MoodEntry::class);
    }

    /**
     * Get the full URL of the selfie image.
     */
    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }

    /**
     * Get the heatmap color based on mood score.
     */
    public function getHeatmapColorAttribute(): string
    {
        return match (true) {
            $this->mood_score_at_capture <= 3 => '#3B82F6', // Blue (low)
            $this->mood_score_at_capture <= 6 => '#8B5CF6', // Purple (medium)
            $this->mood_score_at_capture <= 8 => '#F59E0B', // Orange (good)
            default => '#EF4444', // Red (excellent/intense)
        };
    }
}
