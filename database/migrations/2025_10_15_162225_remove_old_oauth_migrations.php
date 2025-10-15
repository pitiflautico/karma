<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove old OAuth migration entries from migrations table
        // These were created earlier and are being replaced by new migrations with table existence checks
        DB::table('migrations')->whereIn('migration', [
            '2025_10_15_091926_create_oauth_auth_codes_table',
            '2025_10_15_091927_create_oauth_access_tokens_table',
            '2025_10_15_091928_create_oauth_refresh_tokens_table',
            '2025_10_15_091929_create_oauth_clients_table',
            '2025_10_15_091930_create_oauth_device_codes_table',
        ])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to restore old migration entries
    }
};
