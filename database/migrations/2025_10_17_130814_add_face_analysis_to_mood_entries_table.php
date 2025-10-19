<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds fields to store facial analysis data captured during mood entry.
     * This provides objective measurements to complement subjective mood scores.
     */
    public function up(): void
    {
        Schema::table('mood_entries', function (Blueprint $table) {
            // Facial expression analysis
            $table->string('face_expression')->nullable()->after('selfie_taken_at'); // happy, sad, neutral, tired, etc.
            $table->decimal('face_expression_confidence', 5, 4)->nullable()->after('face_expression'); // 0.0000 to 1.0000

            // Energy level detection
            $table->string('face_energy_level')->nullable()->after('face_expression_confidence'); // high, medium, low
            $table->decimal('face_eyes_openness', 5, 4)->nullable()->after('face_energy_level'); // 0.0000 to 1.0000

            // Social context
            $table->string('face_social_context')->nullable()->after('face_eyes_openness'); // alone, with_one, group
            $table->integer('face_total_faces')->nullable()->after('face_social_context'); // number of faces detected

            // Biometric data
            $table->integer('bpm')->nullable()->after('face_total_faces'); // Heart rate (beats per minute)

            // Environmental data
            $table->string('environment_brightness')->nullable()->after('bpm'); // low, medium, high

            // Raw analysis data (JSON for detailed data)
            $table->json('face_analysis_raw')->nullable()->after('environment_brightness');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mood_entries', function (Blueprint $table) {
            $table->dropColumn([
                'face_expression',
                'face_expression_confidence',
                'face_energy_level',
                'face_eyes_openness',
                'face_social_context',
                'face_total_faces',
                'bpm',
                'environment_brightness',
                'face_analysis_raw',
            ]);
        });
    }
};
