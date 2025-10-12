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
        Schema::table('users', function (Blueprint $table) {
            $table->string('push_token')->nullable()->after('settings');
            $table->enum('push_platform', ['ios', 'android'])->nullable()->after('push_token');
            $table->boolean('push_enabled')->default(true)->after('push_platform');
            $table->timestamp('push_registered_at')->nullable()->after('push_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['push_token', 'push_platform', 'push_enabled', 'push_registered_at']);
        });
    }
};
