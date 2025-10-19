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
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->json('attendees')->nullable()->after('location');
            $table->string('organizer_email')->nullable()->after('attendees');
            $table->string('organizer_name')->nullable()->after('organizer_email');
            $table->string('event_link')->nullable()->after('organizer_name');
            $table->string('conference_link')->nullable()->after('event_link');
            $table->string('status')->nullable()->after('conference_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->dropColumn([
                'attendees',
                'organizer_email',
                'organizer_name',
                'event_link',
                'conference_link',
                'status',
            ]);
        });
    }
};
