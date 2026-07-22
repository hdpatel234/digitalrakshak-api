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
        Schema::create('sync_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('job_type', ['invoice_sync', 'payment_sync', 'ticket_sync', 'conversation_sync'])->index('sync_jobs_type');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('config_id')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->nullable()->default('pending')->index('sync_jobs_status');
            $table->integer('items_processed')->nullable()->default(0);
            $table->integer('items_failed')->nullable()->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->longText('sync_log')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_jobs');
    }
};
