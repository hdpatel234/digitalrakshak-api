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
        Schema::create('client_webhooks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->index('client_id');
            $table->string('webhook_name');
            $table->string('webhook_url', 1000)->index('client_webhooks_url');
            $table->string('webhook_secret')->nullable();
            $table->longText('events');
            $table->longText('headers')->nullable();
            $table->enum('format', ['json', 'xml'])->nullable()->default('json');
            $table->integer('max_retries')->nullable()->default(3);
            $table->integer('retry_delay_seconds')->nullable()->default(60);
            $table->integer('timeout_seconds')->nullable()->default(10);
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->timestamp('last_failure_at')->nullable();
            $table->text('last_error')->nullable();
            $table->integer('total_success')->nullable()->default(0);
            $table->integer('total_failures')->nullable()->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('client_webhooks');
    }
};
