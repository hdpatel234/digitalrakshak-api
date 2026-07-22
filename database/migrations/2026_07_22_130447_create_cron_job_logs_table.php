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
        Schema::create('cron_job_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('job_name')->index('idx_job_name');
            $table->enum('job_type', ['email_send', 'email_fetch', 'candidate_import', 'order_processing', 'verification_processing', 'report_generation', 'cleanup', 'scheduled_reminder', 'system_maintenance']);
            $table->timestamp('started_at')->useCurrent()->index('idx_started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->enum('status', ['running', 'completed', 'failed', 'cancelled'])->default('running')->index('idx_status');
            $table->integer('processed_count')->default(0);
            $table->integer('success_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->text('error_message')->nullable();
            $table->json('error_details')->nullable();
            $table->enum('trigger_type', ['manual', 'scheduled', 'webhook', 'system'])->default('scheduled');
            $table->unsignedBigInteger('triggered_by')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->bigInteger('memory_usage')->nullable()->comment('Memory used in bytes');
            $table->decimal('cpu_usage', 5)->nullable()->comment('CPU usage percentage');
            $table->json('details')->nullable()->comment('Additional job details');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cron_job_logs');
    }
};
