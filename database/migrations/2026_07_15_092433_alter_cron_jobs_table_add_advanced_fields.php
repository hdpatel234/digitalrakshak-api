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
        Schema::table('cron_jobs', function (Blueprint $table) {
            $table->string('job_key')->unique()->after('id')->nullable();
            $table->json('parameters')->nullable()->after('schedule');
            $table->string('schedule_type')->default('cron')->after('parameters');
            $table->string('cron_expression')->nullable()->after('schedule_type');
            $table->integer('interval_minutes')->nullable()->after('cron_expression');
            $table->time('time_of_day')->nullable()->after('interval_minutes');
            $table->integer('day_of_week')->nullable()->after('time_of_day');
            $table->integer('day_of_month')->nullable()->after('day_of_week');
            $table->boolean('concurrent_instances')->default(false)->after('day_of_month');
            $table->integer('max_execution_time')->nullable()->after('concurrent_instances');
            $table->string('job_class')->nullable()->after('max_execution_time');
            $table->string('job_method')->nullable()->after('job_class');
            $table->integer('max_retries')->default(3)->after('job_method');
            $table->integer('retry_delay_minutes')->default(5)->after('max_retries');
            $table->integer('priority')->default(0)->after('retry_delay_minutes');
            $table->string('last_run_status')->nullable()->after('priority');
            $table->unsignedBigInteger('last_run_log_id')->nullable()->after('last_run_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cron_jobs', function (Blueprint $table) {
            $table->dropColumn([
                'job_key',
                'parameters',
                'schedule_type',
                'cron_expression',
                'interval_minutes',
                'time_of_day',
                'day_of_week',
                'day_of_month',
                'concurrent_instances',
                'max_execution_time',
                'job_class',
                'job_method',
                'max_retries',
                'retry_delay_minutes',
                'priority',
                'last_run_status',
                'last_run_log_id'
            ]);
        });
    }
};
