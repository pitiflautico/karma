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
        Schema::create('group_event_moods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_event_id')->constrained('group_events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('mood_score'); // 1-10
            $table->string('mood_icon');
            $table->string('mood_name');
            $table->text('note')->nullable();
            $table->timestamps();

            // Un usuario solo puede valorar una vez cada evento
            $table->unique(['group_event_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_event_moods');
    }
};
