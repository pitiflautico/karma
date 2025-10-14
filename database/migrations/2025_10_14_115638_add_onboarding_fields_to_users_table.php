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
            $table->boolean('onboarding_completed')->default(false)->after('email_verified_at');
            $table->date('birth_date')->nullable()->after('onboarding_completed');
            $table->string('gender')->nullable()->after('birth_date');
            $table->float('weight', 8, 2)->nullable()->after('gender')->comment('Weight in kg');
            $table->float('height', 8, 2)->nullable()->after('weight')->comment('Height in cm');
            $table->json('help_reason')->nullable()->after('height')->comment('Reasons user wants help with');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'onboarding_completed',
                'birth_date',
                'gender',
                'weight',
                'height',
                'help_reason',
            ]);
        });
    }
};
