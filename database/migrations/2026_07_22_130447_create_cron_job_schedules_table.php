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
        Schema::create('cron_job_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('job_name')->index('idx_job_name');
            $table->enum('job_type', ['email_send', 'email_fetch', 'candidate_import', 'order_processing', 'verification_processing', 'report_generation', 'cleanup', 'scheduled_reminder', 'system_maintenance']);
            $table->enum('schedule_type', ['cron', 'interval', 'daily', 'weekly', 'monthly', 'custom'])->default('cron');
            $table->string('cron_expression', 100)->nullable();
            $table->integer('interval_seconds')->nullable();
            $table->time('time_of_day')->nullable();
            $table->string('day_of_week', 20)->nullable();
            $table->integer('day_of_month')->nullable();
            $table->integer('month')->nullable();
            $table->string('timezone', 100)->default('UTC');
            $table->integer('max_runtime_seconds')->default(3600);
            $table->integer('max_retries')->default(3);
            $table->integer('retry_delay_seconds')->default(60);
            $table->boolean('concurrent_instances')->default(false)->comment('Allow multiple instances to run simultaneously');
            $table->timestamp('last_run_at')->nullable()->index('idx_last_run');
            $table->timestamp('next_run_at')->nullable()->index('idx_active_next_run');
            $table->enum('last_run_status', ['success', 'failed', 'partial'])->nullable();
            $table->unsignedBigInteger('last_run_log_id')->nullable();
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
        Schema::dropIfExists('cron_job_schedules');
    }
};
