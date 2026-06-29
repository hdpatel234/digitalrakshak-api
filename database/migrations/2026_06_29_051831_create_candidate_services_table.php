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
        Schema::create('candidate_services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('candidate_id');
            $table->integer('service_id');
            $table->unsignedBigInteger('order_id')->nullable()->index('order_id');
            $table->unsignedBigInteger('order_item_id')->nullable()->index('order_item_id');
            $table->decimal('price_paid', 10)->nullable()->default(0);
            $table->unsignedBigInteger('processing_rule_id')->nullable()->index('processing_rule_id');
            $table->enum('processing_status', ['pending', 'processing', 'completed', 'failed', 'retry'])->nullable()->default('pending');
            $table->integer('processing_attempts')->nullable()->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->string('status', 50)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_services');
    }
};
