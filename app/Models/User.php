<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Laravel\Passport\HasApiTokens;
use Filament\Panel;

class User extends Authenticatable implements MustVerifyEmailContract, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, HasUuids, Notifiable, MustVerifyEmail, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'age',
        'password',
        'google_id',
        'google_calendar_token',
        'google_calendar_refresh_token',
        'google_calendar_sync_token',
        'last_calendar_sync_at',
        'calendar_sync_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
        'selfie_mode', // 'random' or 'scheduled'
        'selfie_scheduled_time',
        'adaptive_ui_enabled',
        'last_login_at',
        'settings',
        'push_token',
        'push_platform',
        'push_enabled',
        'push_registered_at',
        'onboarding_completed',
        'birth_date',
        'gender',
        'weight',
        'weight_unit',
        'height',
        'height_unit',
        'help_reason',
        'mood_level',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_calendar_sync_at' => 'datetime',
            'password' => 'hashed',
            'calendar_sync_enabled' => 'boolean',
            'adaptive_ui_enabled' => 'boolean',
            'push_enabled' => 'boolean',
            'push_registered_at' => 'datetime',
            'selfie_scheduled_time' => 'datetime:H:i',
            'settings' => 'array',
            'onboarding_completed' => 'boolean',
            'birth_date' => 'date',
            'help_reason' => 'array',
        ];
    }

    /**
     * Get the user's initials based on their name.
     *
     * Takes the first letter of each word in the name and combines them.
     *
     * @return string The user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Determine if the user can access the Filament admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('admin') && $this->hasVerifiedEmail();
    }

    /**
     * Get the mood entries for the user.
     */
    public function moodEntries(): HasMany
    {
        return $this->hasMany(MoodEntry::class);
    }

    /**
     * Get the calendar events for the user.
     */
    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    /**
     * Get the emotional selfies for the user.
     */
    public function emotionalSelfies(): HasMany
    {
        return $this->hasMany(EmotionalSelfie::class);
    }

    /**
     * Get the groups the user belongs to.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user')
            ->withTimestamps();
    }

    /**
     * Get users who have shared access to this user's data.
     */
    public function sharedWith(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'shared_access', 'owner_id', 'shared_with_user_id')
            ->withPivot(['can_view_moods', 'can_view_notes', 'can_view_selfies'])
            ->withTimestamps();
    }

    /**
     * Get users whose data is shared with this user.
     */
    public function sharedFrom(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'shared_access', 'shared_with_user_id', 'owner_id')
            ->withPivot(['can_view_moods', 'can_view_notes', 'can_view_selfies'])
            ->withTimestamps();
    }

    /**
     * Get the user's subscription.
     */
    public function subscription(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the user's mood prompts.
     */
    public function moodPrompts(): HasMany
    {
        return $this->hasMany(MoodPrompt::class);
    }
}
