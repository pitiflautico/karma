<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'entry_type',
        'selfie_photo_path',
        'selfie_heatmap_path',
        'selfie_taken_at',
        // Face analysis fields
        'face_expression',
        'face_expression_confidence',
        'face_energy_level',
        'face_eyes_openness',
        'face_social_context',
        'face_total_faces',
        'bpm',
        'environment_brightness',
        'face_analysis_raw',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'mood_score' => 'integer',
        'is_manual' => 'boolean',
        'selfie_taken_at' => 'datetime',
        'face_expression_confidence' => 'decimal:4',
        'face_eyes_openness' => 'decimal:4',
        'face_total_faces' => 'integer',
        'bpm' => 'integer',
        'face_analysis_raw' => 'array',
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

    /**
     * Get the mood icon SVG path based on score (1-10).
     */
    public function getMoodIconAttribute(): string
    {
        return match (true) {
            $this->mood_score <= 2 => 'depressed_icon.svg', // 1-2: Depressed
            $this->mood_score <= 4 => 'Sad_icon.svg',       // 3-4: Sad
            $this->mood_score <= 6 => 'Normal_icon.svg',    // 5-6: Normal
            $this->mood_score <= 8 => 'Happy_icon.svg',     // 7-8: Happy
            default => 'Great_icon.svg',                     // 9-10: Great
        };
    }

    /**
     * Get the mood name/label based on score.
     */
    public function getMoodNameAttribute(): string
    {
        return match (true) {
            $this->mood_score <= 2 => 'Depressed',
            $this->mood_score <= 4 => 'Sad',
            $this->mood_score <= 6 => 'Neutral',
            $this->mood_score <= 8 => 'Happy',
            default => 'Overjoyed',
        };
    }

    /**
     * Get the mood background color (hex) based on score.
     */
    public function getMoodColorAttribute(): string
    {
        return match (true) {
            $this->mood_score <= 2 => '#C084FC', // Depressed - Purple
            $this->mood_score <= 4 => '#FB923C', // Sad - Orange
            $this->mood_score <= 6 => '#B1865E', // Normal - Brown
            $this->mood_score <= 8 => '#FBBF24', // Happy - Yellow
            default => '#9BB167',                 // Great - Green
        };
    }

    /**
     * Get the mood Tailwind color class based on score.
     */
    public function getMoodColorClassAttribute(): string
    {
        return match (true) {
            $this->mood_score <= 2 => 'bg-[#C084FC]', // Depressed - Purple
            $this->mood_score <= 4 => 'bg-[#FB923C]', // Sad - Orange
            $this->mood_score <= 6 => 'bg-[#B1865E]', // Normal - Brown
            $this->mood_score <= 8 => 'bg-[#FBBF24]', // Happy - Yellow
            default => 'bg-[#9BB167]',                 // Great - Green
        };
    }

    /**
     * Check if this mood needs doctor consultation (very low).
     */
    public function needsDoctorConsultation(): bool
    {
        return $this->mood_score <= 3;
    }

    /**
     * The tags associated with this mood entry.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'mood_entry_tag');
    }
}
