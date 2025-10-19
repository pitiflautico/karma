<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RevenueCatWebhookController extends Controller
{
    /**
     * Handle RevenueCat webhook events.
     *
     * RevenueCat sends webhooks for various subscription events.
     * Docs: https://www.revenuecat.com/docs/webhooks
     */
    public function handle(Request $request)
    {
        // Verify webhook signature if configured
        if (config('services.revenuecat.webhook_secret')) {
            $signature = $request->header('X-RevenueCat-Signature');
            if (!$this->verifySignature($request->getContent(), $signature)) {
                Log::warning('RevenueCat webhook signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $event = $request->all();
        $eventType = $event['type'] ?? null;

        Log::info('RevenueCat webhook received', [
            'event_type' => $eventType,
            'event_data' => $event,
        ]);

        try {
            switch ($eventType) {
                case 'INITIAL_PURCHASE':
                case 'RENEWAL':
                case 'NON_RENEWING_PURCHASE':
                    $this->handlePurchase($event);
                    break;

                case 'CANCELLATION':
                    $this->handleCancellation($event);
                    break;

                case 'UNCANCELLATION':
                    $this->handleUncancellation($event);
                    break;

                case 'EXPIRATION':
                    $this->handleExpiration($event);
                    break;

                case 'BILLING_ISSUE':
                    $this->handleBillingIssue($event);
                    break;

                case 'PRODUCT_CHANGE':
                    $this->handleProductChange($event);
                    break;

                default:
                    Log::info('Unhandled RevenueCat webhook event type: ' . $eventType);
            }

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error('RevenueCat webhook error', [
                'error' => $e->getMessage(),
                'event' => $event,
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Handle purchase events (initial, renewal, non-renewing).
     */
    protected function handlePurchase(array $event)
    {
        $subscriber = $event['event']['app_user_id'] ?? null;
        $user = $this->findOrCreateUser($subscriber);

        if (!$user) {
            Log::warning('User not found for RevenueCat purchase', ['app_user_id' => $subscriber]);
            return;
        }

        $productId = $event['event']['product_id'] ?? null;
        $entitlementIds = $event['event']['entitlement_ids'] ?? [];
        $store = $event['event']['store'] ?? null;
        $purchaseDate = $event['event']['purchased_at_ms'] ?? null;
        $expirationDate = $event['event']['expiration_at_ms'] ?? null;
        $price = $event['event']['price'] ?? null;
        $currency = $event['event']['currency'] ?? null;
        $isSandbox = $event['event']['is_sandbox'] ?? false;

        // Determine plan name from product ID
        $planName = $this->determinePlanName($productId, $entitlementIds);
        $billingPeriod = $this->determineBillingPeriod($productId);

        // Create or update subscription
        Subscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'revenuecat_customer_id' => $subscriber,
            ],
            [
                'revenuecat_original_app_user_id' => $subscriber,
                'product_id' => $productId,
                'entitlement_id' => $entitlementIds[0] ?? null,
                'store' => $store,
                'store_transaction_id' => $event['event']['transaction_id'] ?? null,
                'store_original_transaction_id' => $event['event']['original_transaction_id'] ?? null,
                'plan_name' => $planName,
                'status' => 'active',
                'billing_period' => $billingPeriod,
                'price' => $price ? $price / 100 : null, // Convert cents to dollars
                'currency' => $currency,
                'will_auto_renew' => $event['event']['auto_resume_at_ms'] ?? true,
                'is_sandbox' => $isSandbox,
                'current_period_start' => $purchaseDate ? now()->setTimestampMs($purchaseDate) : now(),
                'current_period_end' => $expirationDate ? now()->setTimestampMs($expirationDate) : null,
                'expires_at' => $expirationDate ? now()->setTimestampMs($expirationDate) : null,
            ]
        );

        Log::info('RevenueCat purchase processed', [
            'user_id' => $user->id,
            'plan_name' => $planName,
            'product_id' => $productId,
        ]);
    }

    /**
     * Handle cancellation events.
     */
    protected function handleCancellation(array $event)
    {
        $subscriber = $event['event']['app_user_id'] ?? null;
        $user = $this->findOrCreateUser($subscriber);

        if (!$user) {
            return;
        }

        $subscription = Subscription::where('user_id', $user->id)
            ->where('revenuecat_customer_id', $subscriber)
            ->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
                'unsubscribe_detected_at' => now(),
                'will_auto_renew' => false,
            ]);

            Log::info('RevenueCat cancellation processed', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]);
        }
    }

    /**
     * Handle uncancellation events (renewal re-enabled).
     */
    protected function handleUncancellation(array $event)
    {
        $subscriber = $event['event']['app_user_id'] ?? null;
        $user = $this->findOrCreateUser($subscriber);

        if (!$user) {
            return;
        }

        $subscription = Subscription::where('user_id', $user->id)
            ->where('revenuecat_customer_id', $subscriber)
            ->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'active',
                'canceled_at' => null,
                'unsubscribe_detected_at' => null,
                'will_auto_renew' => true,
            ]);

            Log::info('RevenueCat uncancellation processed', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]);
        }
    }

    /**
     * Handle expiration events.
     */
    protected function handleExpiration(array $event)
    {
        $subscriber = $event['event']['app_user_id'] ?? null;
        $user = $this->findOrCreateUser($subscriber);

        if (!$user) {
            return;
        }

        $subscription = Subscription::where('user_id', $user->id)
            ->where('revenuecat_customer_id', $subscriber)
            ->first();

        if ($subscription) {
            $expirationDate = $event['event']['expiration_at_ms'] ?? null;

            $subscription->update([
                'status' => 'expired',
                'expires_at' => $expirationDate ? now()->setTimestampMs($expirationDate) : now(),
            ]);

            Log::info('RevenueCat expiration processed', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]);
        }
    }

    /**
     * Handle billing issue events.
     */
    protected function handleBillingIssue(array $event)
    {
        $subscriber = $event['event']['app_user_id'] ?? null;
        $user = $this->findOrCreateUser($subscriber);

        if (!$user) {
            return;
        }

        $subscription = Subscription::where('user_id', $user->id)
            ->where('revenuecat_customer_id', $subscriber)
            ->first();

        if ($subscription) {
            $gracePeriodExpires = $event['event']['grace_period_expiration_at_ms'] ?? null;

            $subscription->update([
                'billing_issue_detected_at' => now(),
                'grace_period_expires_at' => $gracePeriodExpires ? now()->setTimestampMs($gracePeriodExpires) : null,
            ]);

            Log::info('RevenueCat billing issue detected', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]);
        }
    }

    /**
     * Handle product change events (upgrade/downgrade).
     */
    protected function handleProductChange(array $event)
    {
        $subscriber = $event['event']['app_user_id'] ?? null;
        $user = $this->findOrCreateUser($subscriber);

        if (!$user) {
            return;
        }

        $newProductId = $event['event']['new_product_id'] ?? null;
        $entitlementIds = $event['event']['entitlement_ids'] ?? [];
        $planName = $this->determinePlanName($newProductId, $entitlementIds);
        $billingPeriod = $this->determineBillingPeriod($newProductId);

        $subscription = Subscription::where('user_id', $user->id)
            ->where('revenuecat_customer_id', $subscriber)
            ->first();

        if ($subscription) {
            $subscription->update([
                'product_id' => $newProductId,
                'plan_name' => $planName,
                'billing_period' => $billingPeriod,
                'entitlement_id' => $entitlementIds[0] ?? null,
            ]);

            Log::info('RevenueCat product change processed', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'new_plan' => $planName,
            ]);
        }
    }

    /**
     * Find or create user by app_user_id.
     */
    protected function findOrCreateUser(string $appUserId): ?User
    {
        // Try to find by UUID first (if app_user_id is the user's UUID)
        if (Str::isUuid($appUserId)) {
            return User::find($appUserId);
        }

        // Try to find by email or other identifier
        // This depends on your app's user identification strategy
        return User::where('email', $appUserId)->first();
    }

    /**
     * Determine plan name from product ID.
     */
    protected function determinePlanName(?string $productId, array $entitlementIds = []): string
    {
        if (!$productId) {
            return 'free';
        }

        // Map product IDs to plan names
        // Adjust these based on your actual RevenueCat product setup
        if (Str::contains($productId, ['premium', 'pro'])) {
            return 'premium';
        }

        if (Str::contains($productId, ['enterprise', 'business'])) {
            return 'enterprise';
        }

        // Check entitlements
        foreach ($entitlementIds as $entitlement) {
            if (Str::contains($entitlement, ['premium', 'pro'])) {
                return 'premium';
            }
            if (Str::contains($entitlement, ['enterprise', 'business'])) {
                return 'enterprise';
            }
        }

        return 'free';
    }

    /**
     * Determine billing period from product ID.
     */
    protected function determineBillingPeriod(?string $productId): ?string
    {
        if (!$productId) {
            return null;
        }

        if (Str::contains($productId, ['monthly', 'month', '1m'])) {
            return 'monthly';
        }

        if (Str::contains($productId, ['yearly', 'annual', 'year', '1y'])) {
            return 'yearly';
        }

        if (Str::contains($productId, ['lifetime', 'forever'])) {
            return 'lifetime';
        }

        return 'monthly'; // Default
    }

    /**
     * Verify webhook signature.
     */
    protected function verifySignature(string $payload, ?string $signature): bool
    {
        if (!$signature) {
            return false;
        }

        $secret = config('services.revenuecat.webhook_secret');
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }
}
