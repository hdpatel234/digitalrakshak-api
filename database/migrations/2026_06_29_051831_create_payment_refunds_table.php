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
        Schema::create('payment_refunds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaction_id');
            $table->string('refund_uuid', 100)->unique('refund_uuid');
            $table->string('gateway_refund_id')->nullable();
            $table->decimal('amount', 10);
            $table->text('reason')->nullable();
            $table->enum('status', ['initiated', 'processing', 'success', 'failed'])->nullable()->default('initiated');
            $table->longText('gateway_request')->nullable();
            $table->longText('gateway_response')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('initiated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_refunds');
    }
};
