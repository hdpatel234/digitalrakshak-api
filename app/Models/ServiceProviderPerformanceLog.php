<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProviderPerformanceLog extends BaseModel
{
    use SoftDeletes;
    protected $table = "service_provider_performance_logs";
    const PROVIDER_ID = "provider_id";
    const SERVICE_ID = "service_id";
    const ASSIGNMENT_ID = "assignment_id";
    const RESPONSE_TIME_MS = "response_time_ms";
    const STATUS_CODE = "status_code";
    const SUCCESS = "success";
    const ERROR_MESSAGE = "error_message";
    const LOGGED_AT = "logged_at";
    protected $fillable = [
        self::PROVIDER_ID,
        self::SERVICE_ID,
        self::ASSIGNMENT_ID,
        self::RESPONSE_TIME_MS,
        self::STATUS_CODE,
        self::SUCCESS,
        self::ERROR_MESSAGE,
        self::LOGGED_AT,
    ];
}
