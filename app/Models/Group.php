<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'invite_code',
        'description',
        'avatar',
        'color',
        'created_by',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($group) {
            if (empty($group->invite_code)) {
                $group->invite_code = strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Get the user who created this group.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the users that belong to this group (members).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot(['role', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Get the mood entries for this group (aggregated from members).
     */
    public function moodEntries(): HasMany
    {
        return $this->hasMany(MoodEntry::class);
    }

    /**
     * Get the events for this group.
     */
    public function events(): HasMany
    {
        return $this->hasMany(GroupEvent::class);
    }

    /**
     * Get the average mood for the group within a time period.
     */
    public function getAverageMood(?string $startDate = null, ?string $endDate = null): float
    {
        $query = $this->moodEntries();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->avg('mood_score') ?? 0;
    }
}
