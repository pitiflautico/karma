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
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            // Company Data
            $table->string('company_name')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_website')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('vat_number')->nullable();

            // Billing Parameters
            $table->string('invoice_prefix')->nullable()->default('INV-');
            $table->integer('invoice_sequence')->default(1);
            $table->string('default_tax_type')->nullable();
            $table->string('default_currency')->default('EUR');

            // Email Configuration
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('mailgun_domain')->nullable();
            $table->string('mailgun_secret')->nullable();

            // Legal Texts
            $table->text('legal_text_invoice')->nullable();

            $table->timestamps();

            // Ensure each user has only one settings record
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
