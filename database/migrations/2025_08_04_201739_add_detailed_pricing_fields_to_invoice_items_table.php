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
        Schema::table('invoice_items', function (Blueprint $table) {
            // Add new detailed pricing fields
            $table->bigInteger('price')->after('unit_price')->default(0); // Base price without taxes
            $table->bigInteger('tax_total')->after('tax_rate')->default(0); // Total tax amount
            $table->decimal('irpf_rate', 5, 2)->after('tax_total')->default(0); // IRPF rate (0-100)
            $table->bigInteger('irpf_total')->after('irpf_rate')->default(0); // Total IRPF amount
            $table->bigInteger('total')->after('irpf_total')->default(0); // Final total (price + tax - irpf)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['price', 'tax_total', 'irpf_rate', 'irpf_total', 'total']);
        });
    }
};
