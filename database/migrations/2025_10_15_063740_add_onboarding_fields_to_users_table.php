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
            $table->integer('mood_level')->nullable()->after('help_reason')->comment('User mood level from Step 5');
            $table->string('weight_unit', 10)->default('kg')->after('weight')->comment('Weight unit: kg or lbs');
            $table->string('height_unit', 10)->default('cm')->after('height')->comment('Height unit: cm or inch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'mood_level',
                'weight_unit',
                'height_unit',
            ]);
        });
    }
};
