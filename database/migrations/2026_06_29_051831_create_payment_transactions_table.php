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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transaction_uuid', 100)->unique('transaction_uuid');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('gateway_config_id')->nullable();
            $table->unsignedBigInteger('client_gateway_id')->nullable();
            $table->unsignedBigInteger('method_type_id')->nullable();
            $table->decimal('amount', 10);
            $table->string('currency', 3)->nullable()->default('INR');
            $table->decimal('tax_amount', 10)->nullable()->default(0);
            $table->decimal('fee_amount', 10)->nullable()->default(0);
            $table->decimal('net_amount', 10)->nullable()->storedAs('`amount` - `fee_amount`');
            $table->string('gateway_transaction_id')->nullable()->index('payment_transactions_gateway');
            $table->string('gateway_order_id')->nullable();
            $table->string('gateway_payment_id')->nullable();
            $table->string('bank_reference')->nullable();
            $table->string('payment_method', 100)->nullable();
            $table->longText('payment_details')->nullable();
            $table->string('status')->nullable()->default('initiated');
            $table->string('payment_status')->nullable()->default('pending');
            $table->timestamp('initiated_at')->nullable()->useCurrent();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->timestamp('success_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->longText('gateway_request')->nullable();
            $table->longText('gateway_response')->nullable();
            $table->longText('gateway_webhook')->nullable();
            $table->string('error_code', 100)->nullable();
            $table->text('error_message')->nullable();
            $table->decimal('refund_amount', 10)->nullable()->default(0);
            $table->text('refund_reason')->nullable();
            $table->string('refund_transaction_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable()->index('payment_transactions_customer');
            $table->string('customer_phone', 20)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent()->index('payment_transactions_created');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['status', 'payment_status'], 'payment_transactions_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
