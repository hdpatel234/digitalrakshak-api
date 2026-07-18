<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CronJobExecution extends BaseModel
{
    
    protected $table = "cron_job_executions";
    
    const JOB_KEY = "job_key";
    const STARTED_AT = "started_at";
    const COMPLETED_AT = "completed_at";
    const STATUS = "status";
    const TRIGGERED_BY = "triggered_by";
    const TRIGGERED_BY_USER_ID = "triggered_by_user_id";
    const IP_ADDRESS = "ip_address";
    const DURATION_SECONDS = "duration_seconds";
    const OUTPUT = "output";
    const ERROR_MESSAGE = "error_message";
    const ERROR_STACK = "error_stack";
    const PROCESSED_COUNT = "processed_count";
    const SUCCESS_COUNT = "success_count";
    const FAILED_COUNT = "failed_count";
    const PROCESSED_LOGS = "processed_logs";
    protected $fillable = [
        self::JOB_KEY,
        self::STARTED_AT,
        self::COMPLETED_AT,
        self::STATUS,
        self::TRIGGERED_BY,
        self::TRIGGERED_BY_USER_ID,
        self::IP_ADDRESS,
        self::DURATION_SECONDS,
        self::OUTPUT,
        self::ERROR_MESSAGE,
        self::ERROR_STACK,
        self::PROCESSED_COUNT,
        self::SUCCESS_COUNT,
        self::FAILED_COUNT,
        self::PROCESSED_LOGS,
    ];
}