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
        Schema::table('settings', function (Blueprint $table) {
            // Make invoice_sequence optional (nullable)
            $table->integer('invoice_sequence')->nullable()->default(1)->change();
            
            // Remove default_tax_type field
            $table->dropColumn('default_tax_type');
            
            // Add new fields
            $table->bigInteger('tax_iva')->nullable()->after('default_currency');
            $table->bigInteger('tax_irpf')->nullable()->after('tax_iva');
            $table->bigInteger('goal')->nullable()->after('tax_irpf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Remove new fields
            $table->dropColumn([
                'tax_iva',
                'tax_irpf',
                'goal'
            ]);
            
            // Restore required invoice_sequence (not nullable)
            $table->integer('invoice_sequence')->nullable(false)->default(1)->change();
            
            // Restore default_tax_type field
            $table->string('default_tax_type')->nullable()->after('invoice_sequence');
        });
    }
};
