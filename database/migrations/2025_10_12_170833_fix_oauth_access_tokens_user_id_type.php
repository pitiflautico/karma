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
        // Fix oauth_access_tokens.user_id to match users.id type (varchar/UUID)
        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->string('user_id')->nullable()->change();
        });

        // Also fix oauth_auth_codes.user_id
        Schema::table('oauth_auth_codes', function (Blueprint $table) {
            $table->string('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to integer
        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        Schema::table('oauth_auth_codes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }
};
