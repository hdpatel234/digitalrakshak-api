<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CronJobSchedule extends BaseModel
{
    use SoftDeletes;
    protected $table = "cron_job_schedules";
    const JOB_NAME = "job_name";
    const JOB_TYPE = "job_type";
    const SCHEDULE_TYPE = "schedule_type";
    const CRON_EXPRESSION = "cron_expression";
    const INTERVAL_SECONDS = "interval_seconds";
    const TIME_OF_DAY = "time_of_day";
    const DAY_OF_WEEK = "day_of_week";
    const DAY_OF_MONTH = "day_of_month";
    const MONTH = "month";
    const TIMEZONE = "timezone";
    const MAX_RUNTIME_SECONDS = "max_runtime_seconds";
    const MAX_RETRIES = "max_retries";
    const RETRY_DELAY_SECONDS = "retry_delay_seconds";
    const CONCURRENT_INSTANCES = "concurrent_instances";
    const LAST_RUN_AT = "last_run_at";
    const NEXT_RUN_AT = "next_run_at";
    const LAST_RUN_STATUS = "last_run_status";
    const LAST_RUN_LOG_ID = "last_run_log_id";
    protected $fillable = [
        self::JOB_NAME,
        self::JOB_TYPE,
        self::SCHEDULE_TYPE,
        self::CRON_EXPRESSION,
        self::INTERVAL_SECONDS,
        self::TIME_OF_DAY,
        self::DAY_OF_WEEK,
        self::DAY_OF_MONTH,
        self::MONTH,
        self::TIMEZONE,
        self::MAX_RUNTIME_SECONDS,
        self::MAX_RETRIES,
        self::RETRY_DELAY_SECONDS,
        self::CONCURRENT_INSTANCES,
        self::LAST_RUN_AT,
        self::NEXT_RUN_AT,
        self::LAST_RUN_STATUS,
        self::LAST_RUN_LOG_ID,
        self::STATUS
    ];
}
