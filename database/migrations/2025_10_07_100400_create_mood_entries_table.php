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
        Schema::create('mood_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('calendar_event_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignUuid('group_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('mood_score'); // 1-10 scale
            $table->text('note')->nullable();
            $table->boolean('is_manual')->default(false); // true if not triggered by calendar event
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['group_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mood_entries');
    }
};
