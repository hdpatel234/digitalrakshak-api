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
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('order_id');
            $table->integer('order_candidate_id');
            $table->integer('service_id')->index('idx_order_items_service');
            $table->unsignedBigInteger('support_config_id')->nullable()->index('support_config_id');
            $table->unsignedBigInteger('ticket_id')->nullable()->index('ticket_id');
            $table->unsignedBigInteger('report_document_id')->nullable()->index('report_document_id');
            $table->enum('support_sync_status', ['pending', 'synced', 'failed'])->nullable()->default('pending');
            $table->unsignedBigInteger('processing_rule_id')->nullable()->index('processing_rule_id');
            $table->enum('processing_status', ['pending', 'processing', 'completed', 'failed', 'retry'])->nullable()->default('pending');
            $table->integer('processing_attempts')->nullable()->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->decimal('unit_price', 10);
            $table->integer('quantity')->nullable()->default(1);
            $table->decimal('discount_amount', 10)->nullable()->default(0);
            $table->decimal('tax_amount', 10)->nullable()->default(0);
            $table->decimal('tax_percentage', 5)->nullable()->default(0);
            $table->decimal('total_price', 10);
            $table->longText('service_data')->nullable();
            $table->string('status', 50)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();

            $table->index(['processing_status', 'processing_attempts'], 'idx_order_items_processing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
