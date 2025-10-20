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
        Schema::table('mood_entries', function (Blueprint $table) {
            // Solo agregar si no existe
            if (!Schema::hasColumn('mood_entries', 'entry_type')) {
                $table->string('entry_type')->nullable()->after('is_manual');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mood_entries', function (Blueprint $table) {
            if (Schema::hasColumn('mood_entries', 'entry_type')) {
                $table->dropColumn('entry_type');
            }
        });
    }
};
