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
        Schema::table('mood_entries', function (Blueprint $table) {
            $table->string('selfie_photo_path')->nullable()->after('note');
            $table->string('selfie_heatmap_path')->nullable()->after('selfie_photo_path');
            $table->timestamp('selfie_taken_at')->nullable()->after('selfie_heatmap_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mood_entries', function (Blueprint $table) {
            $table->dropColumn(['selfie_photo_path', 'selfie_heatmap_path', 'selfie_taken_at']);
        });
    }
};
