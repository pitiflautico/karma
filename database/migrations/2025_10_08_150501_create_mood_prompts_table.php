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
        Schema::create('mood_prompts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('calendar_event_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignUuid('mood_entry_id')->nullable()->constrained()->onDelete('set null');
            $table->text('prompt_text');
            $table->string('event_title')->nullable();
            $table->timestamp('event_start_time')->nullable();
            $table->timestamp('event_end_time')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mood_prompts');
    }
};
