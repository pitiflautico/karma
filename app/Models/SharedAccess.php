<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SharedAccess extends Model
{
    use HasUuids;

    protected $table = 'shared_access';

    protected $fillable = [
        'owner_id',
        'shared_with_user_id',
        'can_view_moods',
        'can_view_notes',
        'can_view_selfies',
    ];

    protected $casts = [
        'can_view_moods' => 'boolean',
        'can_view_notes' => 'boolean',
        'can_view_selfies' => 'boolean',
    ];

    /**
     * Get the owner of the shared data.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the user who has access to the shared data.
     */
    public function sharedWithUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }
}
