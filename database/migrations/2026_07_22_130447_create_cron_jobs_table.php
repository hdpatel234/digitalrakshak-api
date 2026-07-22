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
        Schema::create('cron_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('job_key')->nullable()->unique();
            $table->string('job_name')->unique('cron_job_name');
            $table->string('command')->nullable();
            $table->string('schedule')->nullable();
            $table->json('parameters')->nullable();
            $table->string('schedule_type')->default('cron');
            $table->string('cron_expression')->nullable();
            $table->integer('interval_minutes')->nullable();
            $table->time('time_of_day')->nullable();
            $table->integer('day_of_week')->nullable();
            $table->integer('day_of_month')->nullable();
            $table->boolean('concurrent_instances')->default(false);
            $table->integer('max_execution_time')->nullable();
            $table->string('job_class')->nullable();
            $table->string('job_method')->nullable();
            $table->integer('max_retries')->default(3);
            $table->integer('retry_delay_minutes')->default(5);
            $table->integer('priority')->default(0);
            $table->string('last_run_status')->nullable();
            $table->unsignedBigInteger('last_run_log_id')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->integer('processed_count')->nullable()->default(0);
            $table->integer('error_count')->nullable()->default(0);
            $table->text('error_message')->nullable();
            $table->enum('status', ['running', 'completed', 'failed'])->nullable()->default('completed');
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
        Schema::dropIfExists('cron_jobs');
    }
};
