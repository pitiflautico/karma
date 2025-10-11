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
        Schema::table('sharing_invites', function (Blueprint $table) {
            $table->boolean('can_view_moods')->default(true)->after('status');
            $table->boolean('can_view_notes')->default(false)->after('can_view_moods');
            $table->boolean('can_view_selfies')->default(false)->after('can_view_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sharing_invites', function (Blueprint $table) {
            $table->dropColumn(['can_view_moods', 'can_view_notes', 'can_view_selfies']);
        });
    }
};
