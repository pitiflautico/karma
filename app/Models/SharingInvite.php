<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SharingInvite extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_email',
        'token',
        'status',
        'can_view_moods',
        'can_view_notes',
        'can_view_selfies',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'can_view_moods' => 'boolean',
        'can_view_notes' => 'boolean',
        'can_view_selfies' => 'boolean',
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /**
     * Get the sender of the invitation.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Generate a unique invitation token.
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Check if the invitation is still valid.
     */
    public function isValid(): bool
    {
        return $this->status === 'pending' && $this->expires_at->isFuture();
    }

    /**
     * Mark the invitation as accepted.
     */
    public function markAsAccepted(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    /**
     * Mark the invitation as rejected.
     */
    public function markAsRejected(): void
    {
        $this->update([
            'status' => 'rejected',
        ]);
    }

    /**
     * Mark the invitation as expired.
     */
    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired',
        ]);
    }

    /**
     * Scope to get only pending invitations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get only valid (pending and not expired) invitations.
     */
    public function scopeValid($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '>', now());
    }
}
