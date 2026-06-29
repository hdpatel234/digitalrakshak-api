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
        Schema::create('payment_transactions_old', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->index('client_id');
            $table->unsignedBigInteger('invoice_id')->nullable()->index('invoice_id');
            $table->unsignedBigInteger('order_id')->nullable()->index('order_id');
            $table->unsignedBigInteger('billing_config_id');
            $table->string('external_transaction_id')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->decimal('amount', 10);
            $table->string('currency', 3)->nullable()->default('INR');
            $table->enum('status', ['initiated', 'pending', 'success', 'failed', 'refunded'])->nullable()->default('initiated')->index('transactions_status');
            $table->timestamp('transaction_date')->nullable();
            $table->longText('gateway_response')->nullable();
            $table->decimal('refund_amount', 10)->nullable()->default(0);
            $table->text('refund_reason')->nullable();
            $table->timestamp('refund_date')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'failed'])->nullable()->default('pending');
            $table->text('sync_message')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->unique(['billing_config_id', 'external_transaction_id'], 'external_transaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions_old');
    }
};
