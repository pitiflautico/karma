<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupEventMood extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group_event_id',
        'user_id',
        'mood_score',
        'mood_icon',
        'mood_name',
        'note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'mood_score' => 'integer',
    ];

    /**
     * Get the event this mood rating belongs to.
     */
    public function groupEvent(): BelongsTo
    {
        return $this->belongsTo(GroupEvent::class);
    }

    /**
     * Get the user who made this rating.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
