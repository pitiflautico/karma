<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // RevenueCat specific fields
            $table->string('revenuecat_customer_id')->nullable()->after('user_id')->index();
            $table->string('revenuecat_original_app_user_id')->nullable()->after('revenuecat_customer_id');

            // Product/Entitlement information
            $table->string('product_id')->nullable()->after('plan_name'); // e.g., 'premium_monthly', 'premium_yearly'
            $table->string('entitlement_id')->nullable()->after('product_id'); // e.g., 'premium_features'

            // Store information
            $table->string('store')->nullable()->after('entitlement_id'); // 'app_store', 'play_store', 'stripe'
            $table->string('store_transaction_id')->nullable()->after('store');
            $table->string('store_original_transaction_id')->nullable()->after('store_transaction_id');

            // Pricing information
            $table->decimal('price', 10, 2)->nullable()->after('store_original_transaction_id');
            $table->string('currency', 3)->nullable()->after('price'); // ISO 4217 currency code

            // Billing period
            $table->string('billing_period')->nullable()->after('currency'); // 'monthly', 'yearly', 'lifetime'

            // Auto-renewal
            $table->boolean('will_auto_renew')->default(false)->after('status');
            $table->boolean('is_sandbox')->default(false)->after('will_auto_renew');

            // Expiration and grace period
            $table->dateTime('expires_at')->nullable()->after('current_period_end');
            $table->dateTime('grace_period_expires_at')->nullable()->after('expires_at');

            // Unsubscribe detection
            $table->dateTime('unsubscribe_detected_at')->nullable()->after('canceled_at');
            $table->dateTime('billing_issue_detected_at')->nullable()->after('unsubscribe_detected_at');

            // Keep Stripe fields for backward compatibility (rename them to be optional)
            $table->string('stripe_subscription_id')->nullable()->change();
            $table->string('stripe_customer_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'revenuecat_customer_id',
                'revenuecat_original_app_user_id',
                'product_id',
                'entitlement_id',
                'store',
                'store_transaction_id',
                'store_original_transaction_id',
                'price',
                'currency',
                'billing_period',
                'will_auto_renew',
                'is_sandbox',
                'expires_at',
                'grace_period_expires_at',
                'unsubscribe_detected_at',
                'billing_issue_detected_at',
            ]);
        });
    }
};
