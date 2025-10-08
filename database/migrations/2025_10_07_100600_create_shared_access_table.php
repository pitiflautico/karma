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
        Schema::create('shared_access', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('shared_with_user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('can_view_moods')->default(true);
            $table->boolean('can_view_notes')->default(false);
            $table->boolean('can_view_selfies')->default(false);
            $table->timestamps();

            // Prevent duplicate sharing entries
            $table->unique(['owner_id', 'shared_with_user_id']);
            $table->index('shared_with_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_access');
    }
};
