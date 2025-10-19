<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        // Stripe (legacy)
        'stripe_subscription_id',
        'stripe_customer_id',
        // RevenueCat
        'revenuecat_customer_id',
        'revenuecat_original_app_user_id',
        // Product/Entitlement
        'product_id',
        'entitlement_id',
        // Store
        'store',
        'store_transaction_id',
        'store_original_transaction_id',
        // Plan & Status
        'plan_name',
        'status',
        // Pricing
        'price',
        'currency',
        'billing_period',
        // Renewal & Sandbox
        'will_auto_renew',
        'is_sandbox',
        // Dates
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'expires_at',
        'grace_period_expires_at',
        'canceled_at',
        'unsubscribe_detected_at',
        'billing_issue_detected_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'expires_at' => 'datetime',
        'grace_period_expires_at' => 'datetime',
        'canceled_at' => 'datetime',
        'unsubscribe_detected_at' => 'datetime',
        'billing_issue_detected_at' => 'datetime',
        'will_auto_renew' => 'boolean',
        'is_sandbox' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the subscription is on trial.
     */
    public function onTrial(): bool
    {
        return $this->status === 'trial' && $this->trial_ends_at?->isFuture();
    }

    /**
     * Check if the subscription is canceled.
     */
    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    /**
     * Check if the subscription has expired.
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Check if the subscription is in grace period.
     */
    public function isInGracePeriod(): bool
    {
        return $this->grace_period_expires_at && $this->grace_period_expires_at->isFuture();
    }

    /**
     * Check if this is a free plan.
     */
    public function isFree(): bool
    {
        return $this->plan_name === 'free';
    }

    /**
     * Check if this is a paid plan.
     */
    public function isPaid(): bool
    {
        return in_array($this->plan_name, ['premium', 'enterprise']);
    }

    /**
     * Get the store display name.
     */
    public function getStoreDisplayName(): ?string
    {
        return match($this->store) {
            'app_store' => 'App Store',
            'play_store' => 'Google Play',
            'stripe' => 'Stripe',
            default => $this->store,
        };
    }

    /**
     * Get the billing period display name.
     */
    public function getBillingPeriodDisplayName(): ?string
    {
        return match($this->billing_period) {
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
            'lifetime' => 'Lifetime',
            default => $this->billing_period,
        };
    }

    /**
     * Check if subscription has billing issues.
     */
    public function hasBillingIssues(): bool
    {
        return $this->billing_issue_detected_at !== null;
    }

    /**
     * Get the active subscription for a user (most recent active/trial).
     */
    public static function activeForUser(string $userId): ?self
    {
        return self::where('user_id', $userId)
            ->whereIn('status', ['active', 'trial'])
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
