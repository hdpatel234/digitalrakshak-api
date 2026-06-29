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
        Schema::create('service_processing_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_id')->index('service_id');
            $table->enum('processing_type', ['api', 'email', 'manual_ticket', 'cron', 'webhook'])->index('service_processing_type');
            $table->string('api_endpoint', 500)->nullable();
            $table->string('api_method', 10)->nullable();
            $table->longText('api_headers')->nullable();
            $table->longText('api_mapping')->nullable();
            $table->unsignedBigInteger('email_template_id')->nullable();
            $table->string('email_to', 500)->nullable();
            $table->enum('ticket_priority', ['low', 'medium', 'high', 'urgent'])->nullable()->default('medium');
            $table->string('ticket_department', 100)->nullable();
            $table->string('cron_expression', 100)->nullable();
            $table->string('webhook_url', 500)->nullable();
            $table->string('webhook_secret')->nullable();
            $table->integer('timeout_seconds')->nullable()->default(30);
            $table->integer('retry_count')->nullable()->default(3);
            $table->integer('retry_delay_minutes')->nullable()->default(5);
            $table->string('success_status', 50)->nullable()->default('completed');
            $table->string('failure_status', 50)->nullable()->default('failed');
            $table->boolean('is_active')->nullable()->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_processing_rules');
    }
};
