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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('prefix', 10)->default('')->after('organization_id');
            $table->unsignedInteger('number')->default(1)->after('prefix');
            $table->dropColumn('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_number')->after('organization_id');
            $table->dropColumn(['prefix', 'number']);
        });
    }
};
