<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CronJob extends Model
{
    protected $fillable = [
        'job_name',
        'job_key',
        'command',
        'schedule',
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
        'is_active',
        'last_run_at',
        'next_run_at',
        'status',
        'last_run_status',
        'last_run_log_id',
        'duration_seconds',
        'processed_count',
        'error_count',
        'error_message',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'concurrent_instances' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'parameters' => 'array',
    ];

    public function executions()
    {
        return $this->hasMany(CronJobExecution::class, 'job_key', 'job_key');
    }
}
