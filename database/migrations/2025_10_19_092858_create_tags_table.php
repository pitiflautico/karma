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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tag name (e.g., "Work Stress", "Exercise", "Social")
            $table->string('emoji')->nullable(); // Optional emoji for the tag
            $table->string('category')->nullable(); // Category grouping (e.g., "activity", "feeling", "location")
            $table->json('mood_associations')->nullable(); // JSON array of mood scores this tag is suggested for
            $table->boolean('is_custom')->default(false); // Whether this was user-created or predefined
            $table->foreignUuid('user_id')->nullable()->constrained()->onDelete('cascade'); // Null for system tags, user_id for custom tags
            $table->timestamps();

            $table->index('category');
            $table->index(['user_id', 'is_custom']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
