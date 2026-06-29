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
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->index('client_id');
            $table->unsignedBigInteger('order_id')->nullable()->index('order_id');
            $table->unsignedBigInteger('billing_config_id');
            $table->string('external_invoice_id')->nullable();
            $table->string('external_invoice_number', 100)->nullable();
            $table->string('invoice_number', 100);
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 10)->default(0);
            $table->decimal('discount_amount', 10)->nullable()->default(0);
            $table->decimal('tax_amount', 10)->nullable()->default(0);
            $table->decimal('tax_percentage', 5)->nullable()->default(0);
            $table->decimal('total_amount', 10)->default(0);
            $table->decimal('amount_paid', 10)->nullable()->default(0);
            $table->decimal('amount_due', 10)->nullable()->default(0);
            $table->string('currency', 3)->nullable()->default('INR');
            $table->string('status')->nullable()->default('draft')->index('invoices_status');
            $table->string('payment_status')->nullable()->default('pending');
            $table->string('pdf_url', 1000)->nullable();
            $table->string('sync_status')->nullable()->default('pending')->index('invoices_sync_status');
            $table->text('sync_message')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->longText('invoice_data')->nullable();
            $table->unsignedBigInteger('document_id')->nullable()->index('document_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['billing_config_id', 'external_invoice_id'], 'external_invoice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
