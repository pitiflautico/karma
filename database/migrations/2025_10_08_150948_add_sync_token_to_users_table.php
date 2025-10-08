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
            $table->text('google_calendar_sync_token')->nullable()->after('google_calendar_refresh_token');
            $table->timestamp('last_calendar_sync_at')->nullable()->after('google_calendar_sync_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_calendar_sync_token', 'last_calendar_sync_at']);
        });
    }
};
