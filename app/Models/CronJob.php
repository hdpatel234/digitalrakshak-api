<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CronJob extends Model
{
    use SoftDeletes;

    const JOB_NAME = 'job_name';
    const JOB_KEY = 'job_key';
    const COMMAND = 'command';
    const SCHEDULE = 'schedule';
    const PARAMETERS = 'parameters';
    const SCHEDULE_TYPE = 'schedule_type';
    const CRON_EXPRESSION = 'cron_expression';
    const INTERVAL_MINUTES = 'interval_minutes';
    const TIME_OF_DAY = 'time_of_day';
    const DAY_OF_WEEK = 'day_of_week';
    const DAY_OF_MONTH = 'day_of_month';
    const CONCURRENT_INSTANCES = 'concurrent_instances';
    const MAX_EXECUTION_TIME = 'max_execution_time';
    const JOB_CLASS = 'job_class';
    const JOB_METHOD = 'job_method';
    const MAX_RETRIES = 'max_retries';
    const RETRY_DELAY_MINUTES = 'retry_delay_minutes';
    const PRIORITY = 'priority';
    const IS_ACTIVE = 'is_active';
    const LAST_RUN_AT = 'last_run_at';
    const NEXT_RUN_AT = 'next_run_at';
    const STATUS = 'status';
    const LAST_RUN_STATUS = 'last_run_status';
    const LAST_RUN_LOG_ID = 'last_run_log_id';
    const DURATION_SECONDS = 'duration_seconds';
    const PROCESSED_COUNT = 'processed_count';
    const ERROR_COUNT = 'error_count';
    const ERROR_MESSAGE = 'error_message';

    protected $fillable = [
        self::JOB_NAME,
        self::JOB_KEY,
        self::COMMAND,
        self::SCHEDULE,
        self::PARAMETERS,
        self::SCHEDULE_TYPE,
        self::CRON_EXPRESSION,
        self::INTERVAL_MINUTES,
        self::TIME_OF_DAY,
        self::DAY_OF_WEEK,
        self::DAY_OF_MONTH,
        self::CONCURRENT_INSTANCES,
        self::MAX_EXECUTION_TIME,
        self::JOB_CLASS,
        self::JOB_METHOD,
        self::MAX_RETRIES,
        self::RETRY_DELAY_MINUTES,
        self::PRIORITY,
        self::IS_ACTIVE,
        self::LAST_RUN_AT,
        self::NEXT_RUN_AT,
        self::STATUS,
        self::LAST_RUN_STATUS,
        self::LAST_RUN_LOG_ID,
        self::DURATION_SECONDS,
        self::PROCESSED_COUNT,
        self::ERROR_COUNT,
        self::ERROR_MESSAGE,
    ];

    protected $casts = [
        self::IS_ACTIVE => 'boolean',
        self::CONCURRENT_INSTANCES => 'boolean',
        self::LAST_RUN_AT => 'datetime',
        self::NEXT_RUN_AT => 'datetime',
        self::PARAMETERS => 'array',
    ];

    public function executions()
    {
        return $this->hasMany(CronJobExecution::class, 'job_key', 'job_key');
    }
}
