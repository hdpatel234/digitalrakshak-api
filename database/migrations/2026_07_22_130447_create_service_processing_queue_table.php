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
        Schema::create('service_processing_queue', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_item_id')->index('order_item_id');
            $table->unsignedBigInteger('service_id')->index('idx_processing_queue_service');
            $table->unsignedBigInteger('candidate_id')->index('candidate_id');
            $table->unsignedBigInteger('processing_rule_id')->index('processing_rule_id');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'retry'])->nullable()->default('pending')->index('queue_status_index');
            $table->integer('attempts')->nullable()->default(0);
            $table->integer('max_attempts')->nullable()->default(3);
            $table->longText('request_data')->nullable();
            $table->longText('response_data')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('next_retry_at')->nullable()->index('queue_next_retry_index');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->index(['status', 'next_retry_at'], 'idx_processing_queue_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_processing_queue');
    }
};
