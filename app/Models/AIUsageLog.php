<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIUsageLog extends Model
{
    protected $table = 'ai_usage_logs';

    protected $fillable = [
        'user_id',
        'service',
        'model',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'estimated_cost',
        'request_type',
    ];

    protected $casts = [
        'input_tokens' => 'integer',
        'output_tokens' => 'integer',
        'total_tokens' => 'integer',
        'estimated_cost' => 'decimal:6',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get total cost for a user
     */
    public static function getTotalCostForUser(string $userId): float
    {
        return self::where('user_id', $userId)->sum('estimated_cost');
    }

    /**
     * Get usage statistics for a user
     */
    public static function getUsageStatsForUser(string $userId, int $days = 30): array
    {
        $logs = self::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        return [
            'total_requests' => $logs->count(),
            'total_tokens' => $logs->sum('total_tokens'),
            'total_cost' => $logs->sum('estimated_cost'),
            'by_service' => $logs->groupBy('service')->map(function ($serviceLogs) {
                return [
                    'count' => $serviceLogs->count(),
                    'tokens' => $serviceLogs->sum('total_tokens'),
                    'cost' => $serviceLogs->sum('estimated_cost'),
                ];
            }),
        ];
    }
}
