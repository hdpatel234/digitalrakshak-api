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
        Schema::create('client_webhook_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->index('client_id');
            $table->unsignedBigInteger('webhook_id');
            $table->string('event_type', 100)->index('webhook_logs_event');
            $table->longText('payload');
            $table->longText('headers')->nullable();
            $table->integer('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->integer('attempt')->nullable()->default(1);
            $table->enum('status', ['success', 'failed', 'pending', 'retrying'])->nullable()->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('next_retry_at')->nullable()->index('webhook_logs_retry');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->index(['webhook_id', 'status'], 'webhook_logs_webhook');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_webhook_logs');
    }
};
