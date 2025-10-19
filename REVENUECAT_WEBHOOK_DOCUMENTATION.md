# RevenueCat Webhook Integration Documentation

## Overview

This document explains how to integrate RevenueCat webhooks with your Laravel backend to automatically sync subscription status and handle in-app purchase events.

## What is RevenueCat?

RevenueCat is a subscription management platform that handles in-app purchases across iOS, Android, and web platforms. It provides webhooks to notify your backend when subscription events occur.

**Official Documentation**: https://www.revenuecat.com/docs/webhooks

## Webhook Events

The system handles the following RevenueCat webhook events:

| Event Type | Description | Action Taken |
|------------|-------------|--------------|
| `INITIAL_PURCHASE` | User makes their first purchase | Create subscription record |
| `RENEWAL` | Subscription renews automatically | Update subscription, extend period |
| `NON_RENEWING_PURCHASE` | One-time purchase (lifetime) | Create subscription with no expiry |
| `CANCELLATION` | User cancels subscription | Mark as canceled, disable auto-renew |
| `UNCANCELLATION` | User re-enables subscription | Reactivate subscription |
| `EXPIRATION` | Subscription expires | Mark as expired |
| `BILLING_ISSUE` | Payment fails | Set billing issue flag, grace period |
| `PRODUCT_CHANGE` | Upgrade/downgrade plan | Update product ID and plan name |

---

## Setup Instructions

### 1. Configure RevenueCat Webhook Secret

Add your RevenueCat webhook authorization key to `.env`:

```bash
REVENUECAT_WEBHOOK_SECRET=your_webhook_secret_here
```

Get this from RevenueCat dashboard:
1. Go to **Project Settings** → **Integrations** → **Webhooks**
2. Copy the **Authorization Key**
3. Add it to your `.env` file

Also configure in `config/services.php`:

```php
'revenuecat' => [
    'api_key' => env('REVENUECAT_API_KEY'),
    'webhook_secret' => env('REVENUECAT_WEBHOOK_SECRET'),
],
```

### 2. Register Webhook URL in RevenueCat

1. Go to RevenueCat Dashboard
2. Navigate to **Project Settings** → **Integrations** → **Webhooks**
3. Click **+ Add Webhook**
4. Enter your webhook URL:
   ```
   https://your-domain.com/api/revenuecat/webhook
   ```
5. Select events to send (select all or specific events)
6. Save

### 3. Add Webhook Route

Ensure the route is registered in `routes/api.php`:

```php
// RevenueCat webhook (no auth required, verified by signature)
Route::post('/revenuecat/webhook', [RevenueCatWebhookController::class, 'handle']);
```

**IMPORTANT**: This route should NOT have the `auth:sanctum` middleware, as RevenueCat sends webhooks without user authentication. Security is handled via signature verification.

### 4. Run Migrations

Ensure subscription fields are added:

```bash
php artisan migrate
```

The migration adds these fields to the `subscriptions` table:
- `revenuecat_customer_id`
- `revenuecat_original_app_user_id`
- `product_id`
- `entitlement_id`
- `store` (app_store, play_store, stripe, etc.)
- `store_transaction_id`
- `store_original_transaction_id`
- `price` and `currency`
- `will_auto_renew`
- `is_sandbox`
- `current_period_start` and `current_period_end`
- `billing_issue_detected_at`
- `grace_period_expires_at`

---

## Webhook Request Format

### Headers

```http
POST /api/revenuecat/webhook
Content-Type: application/json
X-RevenueCat-Signature: {hmac_signature}
```

### Example Payload (INITIAL_PURCHASE)

```json
{
  "api_version": "1.0",
  "event": {
    "type": "INITIAL_PURCHASE",
    "app_user_id": "user-uuid-or-email",
    "product_id": "premium_monthly",
    "entitlement_ids": ["premium"],
    "store": "app_store",
    "transaction_id": "1000000123456789",
    "original_transaction_id": "1000000123456789",
    "purchased_at_ms": 1697721600000,
    "expiration_at_ms": 1700313600000,
    "price": 999,
    "currency": "USD",
    "is_sandbox": false,
    "auto_resume_at_ms": null
  }
}
```

### Example Payload (CANCELLATION)

```json
{
  "api_version": "1.0",
  "event": {
    "type": "CANCELLATION",
    "app_user_id": "user-uuid-or-email",
    "product_id": "premium_monthly",
    "entitlement_ids": ["premium"],
    "store": "app_store",
    "expiration_at_ms": 1700313600000,
    "cancel_reason": "CUSTOMER_CANCELLED"
  }
}
```

### Example Payload (BILLING_ISSUE)

```json
{
  "api_version": "1.0",
  "event": {
    "type": "BILLING_ISSUE",
    "app_user_id": "user-uuid-or-email",
    "product_id": "premium_monthly",
    "entitlement_ids": ["premium"],
    "store": "play_store",
    "grace_period_expiration_at_ms": 1697808000000
  }
}
```

---

## Response Format

### Success Response (200)

```json
{
  "success": true
}
```

### Error Response (401 - Invalid Signature)

```json
{
  "error": "Invalid signature"
}
```

### Error Response (500 - Server Error)

```json
{
  "error": "Internal server error"
}
```

---

## Security

### Webhook Signature Verification

The webhook controller automatically verifies the signature sent by RevenueCat:

```php
// In RevenueCatWebhookController.php
protected function verifySignature(string $payload, ?string $signature): bool
{
    if (!$signature) {
        return false;
    }

    $secret = config('services.revenuecat.webhook_secret');
    $expectedSignature = hash_hmac('sha256', $payload, $secret);

    return hash_equals($expectedSignature, $signature);
}
```

If the signature doesn't match, the webhook returns a `401 Unauthorized` response.

---

## Event Processing Logic

### INITIAL_PURCHASE / RENEWAL / NON_RENEWING_PURCHASE

1. Find or create user by `app_user_id`
2. Determine plan name from `product_id`
3. Determine billing period (monthly, yearly, lifetime)
4. Create or update subscription record:
   - Set status to `active`
   - Store product ID, transaction IDs
   - Set current period dates
   - Store price and currency
   - Mark sandbox flag

### CANCELLATION

1. Find subscription by `revenuecat_customer_id`
2. Update subscription:
   - Set status to `canceled`
   - Set `canceled_at` timestamp
   - Set `will_auto_renew` to false
   - Set `unsubscribe_detected_at`

### UNCANCELLATION

1. Find subscription
2. Update subscription:
   - Set status to `active`
   - Clear `canceled_at` and `unsubscribe_detected_at`
   - Set `will_auto_renew` to true

### EXPIRATION

1. Find subscription
2. Update subscription:
   - Set status to `expired`
   - Set `expires_at` timestamp

### BILLING_ISSUE

1. Find subscription
2. Update subscription:
   - Set `billing_issue_detected_at`
   - Set `grace_period_expires_at` (if provided)

### PRODUCT_CHANGE

1. Find subscription
2. Update subscription:
   - Update `product_id` to new product
   - Update `plan_name` and `billing_period`
   - Update `entitlement_id`

---

## Plan Name Mapping

The controller automatically determines plan names from product IDs:

```php
protected function determinePlanName(?string $productId, array $entitlementIds = []): string
{
    // Check product ID for keywords
    if (Str::contains($productId, ['premium', 'pro'])) {
        return 'premium';
    }

    if (Str::contains($productId, ['enterprise', 'business'])) {
        return 'enterprise';
    }

    // Check entitlement IDs
    // ...

    return 'free'; // Default
}
```

**Customize this method** based on your actual product IDs in RevenueCat.

### Example Product IDs

Common naming conventions:
- `premium_monthly` → Plan: premium, Period: monthly
- `premium_yearly` → Plan: premium, Period: yearly
- `pro_lifetime` → Plan: premium, Period: lifetime
- `basic_monthly` → Plan: free, Period: monthly

---

## User Identification

The webhook identifies users via `app_user_id` sent by RevenueCat:

```php
protected function findOrCreateUser(string $appUserId): ?User
{
    // If app_user_id is a UUID (recommended)
    if (Str::isUuid($appUserId)) {
        return User::find($appUserId);
    }

    // If app_user_id is an email
    return User::where('email', $appUserId)->first();
}
```

### Recommended Setup

In your mobile app, set the RevenueCat user ID to match your backend user ID:

```javascript
// React Native
import Purchases from 'react-native-purchases';

// After user logs in
await Purchases.logIn(user.id); // Use backend user UUID
```

This ensures webhooks can correctly match to your users.

---

## Testing

### 1. Test with RevenueCat Sandbox

Enable sandbox testing in RevenueCat:
1. Make test purchases in iOS/Android sandbox environment
2. Webhooks will have `is_sandbox: true`
3. Check Laravel logs for webhook processing

### 2. Manual Testing with cURL

```bash
# Test INITIAL_PURCHASE event
curl -X POST "https://your-domain.com/api/revenuecat/webhook" \
  -H "Content-Type: application/json" \
  -H "X-RevenueCat-Signature: YOUR_HMAC_SIGNATURE" \
  -d '{
    "api_version": "1.0",
    "event": {
      "type": "INITIAL_PURCHASE",
      "app_user_id": "test-user-uuid",
      "product_id": "premium_monthly",
      "entitlement_ids": ["premium"],
      "store": "app_store",
      "purchased_at_ms": 1697721600000,
      "expiration_at_ms": 1700313600000,
      "price": 999,
      "currency": "USD",
      "is_sandbox": true
    }
  }'
```

To generate the signature:
```bash
# In terminal
echo -n 'YOUR_JSON_PAYLOAD' | openssl dgst -sha256 -hmac 'YOUR_WEBHOOK_SECRET'
```

### 3. Use RevenueCat Test Events

RevenueCat provides test events in their dashboard:
1. Go to **RevenueCat Dashboard** → **Webhooks**
2. Click **Send Test Event**
3. Select event type
4. Check your Laravel logs

---

## Monitoring and Debugging

### Check Webhook Logs

```bash
# Watch Laravel logs
tail -f storage/logs/laravel.log | grep -i "revenuecat"
```

Webhook processing logs include:
- `RevenueCat webhook received` - Event type and data
- `RevenueCat purchase processed` - Successful purchase/renewal
- `RevenueCat cancellation processed` - Successful cancellation
- `RevenueCat webhook signature verification failed` - Security issue
- `RevenueCat webhook error` - Processing error

### Verify Subscription Status

```bash
php artisan tinker

# Check user's subscription
>>> $user = User::find('uuid');
>>> $user->subscription;
>>> $user->subscription->status;
>>> $user->subscription->plan_name;
>>> $user->subscription->expires_at;
```

### Test Webhook Delivery

RevenueCat dashboard shows webhook delivery status:
1. Go to **Webhooks** tab
2. Click on a webhook URL
3. View **Recent Deliveries**
4. Check response codes and retry attempts

---

## Common Issues

### Issue: Webhook Returns 401 (Signature Invalid)

**Cause**: `REVENUECAT_WEBHOOK_SECRET` doesn't match RevenueCat's authorization key

**Solution**:
1. Copy exact authorization key from RevenueCat dashboard
2. Update `.env` file
3. Clear config cache: `php artisan config:clear`

### Issue: User Not Found

**Cause**: `app_user_id` doesn't match any user in database

**Solution**:
1. Ensure mobile app calls `Purchases.logIn(user.id)` after authentication
2. Verify `app_user_id` format matches user identification strategy
3. Check logs: `RevenueCat webhook received` will show the `app_user_id`

### Issue: Subscription Not Updating

**Cause**: Webhook processing error or database issue

**Solution**:
1. Check Laravel logs for exceptions
2. Verify subscription table has all required columns
3. Run migrations: `php artisan migrate`
4. Check `updateOrCreate` logic in controller

### Issue: Duplicate Subscriptions

**Cause**: Multiple webhook deliveries or unique constraint missing

**Solution**:
1. Add unique index on `revenuecat_customer_id`:
   ```php
   $table->unique('revenuecat_customer_id');
   ```
2. Use `updateOrCreate` instead of `create` (already implemented)

---

## Production Checklist

- [ ] `REVENUECAT_WEBHOOK_SECRET` configured in `.env`
- [ ] Webhook URL registered in RevenueCat dashboard
- [ ] Webhook route added to `routes/api.php` (without auth middleware)
- [ ] Migrations run: `php artisan migrate`
- [ ] Signature verification enabled and working
- [ ] Plan name mapping customized for your products
- [ ] User identification strategy matches mobile app
- [ ] Tested with sandbox purchases
- [ ] Logs monitored for webhook processing
- [ ] Error handling verified
- [ ] SSL certificate valid (RevenueCat requires HTTPS)

---

## Architecture Diagram

```
┌─────────────────────┐
│   Mobile App        │
│   (iOS/Android)     │
│                     │
│  ┌───────────────┐  │
│  │  RevenueCat   │  │
│  │  SDK          │  │
│  └───────┬───────┘  │
└──────────┼──────────┘
           │
           │ Purchase Event
           ▼
┌─────────────────────────┐
│   RevenueCat            │
│   Backend               │
│                         │
│  ┌──────────────────┐   │
│  │  Process Event   │   │
│  │  Update Status   │   │
│  └────────┬─────────┘   │
│           │             │
│           │ Send Webhook│
└───────────┼─────────────┘
            │
            │ POST /api/revenuecat/webhook
            ▼
┌─────────────────────────────────┐
│   Your Laravel Backend          │
│                                 │
│  ┌──────────────────────────┐   │
│  │ RevenueCat               │   │
│  │ WebhookController        │   │
│  │                          │   │
│  │ 1. Verify Signature      │   │
│  │ 2. Parse Event Type      │   │
│  │ 3. Find/Create User      │   │
│  │ 4. Update Subscription   │   │
│  └────────┬─────────────────┘   │
│           │                     │
│           ▼                     │
│  ┌──────────────────────────┐   │
│  │ Subscription Model       │   │
│  │ - status: active         │   │
│  │ - plan_name: premium     │   │
│  │ - expires_at: ...        │   │
│  └──────────────────────────┘   │
└─────────────────────────────────┘
```

---

## API Reference

### Webhook Endpoint

**URL**: `POST /api/revenuecat/webhook`

**Authentication**: Signature verification via `X-RevenueCat-Signature` header

**Content-Type**: `application/json`

**Request Body**: RevenueCat webhook event (see examples above)

**Response Codes**:
- `200` - Success
- `401` - Invalid signature
- `500` - Server error

---

## Additional Resources

- [RevenueCat Webhook Documentation](https://www.revenuecat.com/docs/webhooks)
- [RevenueCat Event Types](https://www.revenuecat.com/docs/webhooks/event-types)
- [RevenueCat Webhook Integration](https://www.revenuecat.com/docs/webhooks/integrations)
- [Laravel Subscriptions Documentation](https://laravel.com/docs/billing)

---

**Last Updated**: October 19, 2025
**API Version**: 1.0
**Contact**: development@feelith.com
