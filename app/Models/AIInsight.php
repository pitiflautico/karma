<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIInsight extends Model
{
    protected $table = 'ai_insights';

    protected $fillable = [
        'user_id',
        'period',
        'insights_data',
        'summary_stats',
        'generated_at',
    ];

    protected $casts = [
        'insights_data' => 'array',
        'summary_stats' => 'array',
        'generated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the insight
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if the insight is still fresh (within 24 hours)
     */
    public function isFresh(): bool
    {
        return $this->generated_at->diffInHours(now()) < 24;
    }

    /**
     * Get the most recent insight for a user and period
     */
    public static function getLatest(string $userId, string $period): ?self
    {
        return self::where('user_id', $userId)
            ->where('period', $period)
            ->orderBy('generated_at', 'desc')
            ->first();
    }
}
