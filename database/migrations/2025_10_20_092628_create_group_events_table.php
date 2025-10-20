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
        Schema::create('group_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('calendar_event_id')->nullable()->constrained('calendar_events')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('event_date');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_custom')->default(false); // true si es creado manualmente, false si viene de calendar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_events');
    }
};
