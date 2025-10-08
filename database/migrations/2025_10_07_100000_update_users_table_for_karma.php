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
            // Remove organization_id if it exists
            if (Schema::hasColumn('users', 'organization_id')) {
                $table->dropForeign(['organization_id']);
                $table->dropColumn('organization_id');
            }

            // Add Google OAuth fields
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->text('google_calendar_token')->nullable();
            $table->text('google_calendar_refresh_token')->nullable();

            // Calendar sync settings
            $table->boolean('calendar_sync_enabled')->default(false);
            $table->time('quiet_hours_start')->nullable();
            $table->time('quiet_hours_end')->nullable();

            // Emotional selfie settings
            $table->enum('selfie_mode', ['random', 'scheduled'])->default('random');
            $table->time('selfie_scheduled_time')->nullable();

            // UI preferences
            $table->boolean('adaptive_ui_enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id',
                'google_calendar_token',
                'google_calendar_refresh_token',
                'calendar_sync_enabled',
                'quiet_hours_start',
                'quiet_hours_end',
                'selfie_mode',
                'selfie_scheduled_time',
                'adaptive_ui_enabled',
            ]);
        });
    }
};
