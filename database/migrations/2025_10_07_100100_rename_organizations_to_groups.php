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
        // Rename organizations table to groups
        Schema::rename('organizations', 'groups');

        // Add new fields to groups table
        Schema::table('groups', function (Blueprint $table) {
            $table->string('invite_code')->unique()->after('slug');
            $table->text('description')->nullable()->after('invite_code');
            $table->boolean('is_active')->default(true)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['invite_code', 'description', 'is_active']);
        });

        Schema::rename('groups', 'organizations');
    }
};
