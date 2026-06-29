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
        Schema::create('candidate_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_number', 50)->unique('order_number');
            $table->string('client_order_number', 100)->nullable();
            $table->integer('client_id');
            $table->unsignedBigInteger('billing_config_id')->nullable()->index('tblcandidate_orders_ibfk_1');
            $table->unsignedBigInteger('invoice_id')->nullable()->index('invoice_id');
            $table->string('billing_sync_status')->nullable()->default('pending');
            $table->text('billing_sync_message')->nullable();
            $table->integer('package_id')->nullable();
            $table->enum('order_type', ['package', 'custom'])->default('package');
            $table->decimal('subtotal', 10)->default(0);
            $table->decimal('discount_amount', 10)->nullable()->default(0);
            $table->decimal('tax_amount', 10)->nullable()->default(0);
            $table->decimal('tax_percentage', 5)->nullable()->default(0);
            $table->decimal('total_amount', 10)->default(0);
            $table->string('payment_status')->nullable()->default('pending')->index('idx_orders_payment');
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_reference', 100)->nullable();
            $table->date('payment_due_date')->nullable();
            $table->string('invoice_number', 100)->nullable();
            $table->timestamp('invoice_generated_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamp('order_date')->useCurrent()->index('idx_orders_date');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->string('status')->nullable()->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['client_id', 'status'], 'idx_orders_client_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_orders');
    }
};
