<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProcessingRule extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "service_processing_rules";
    
    const SERVICE_ID = "service_id";
    const PROCESSING_TYPE = "processing_type";
    const API_ENDPOINT = "api_endpoint";
    const API_METHOD = "api_method";
    const API_HEADERS = "api_headers";
    const API_MAPPING = "api_mapping";
    const EMAIL_TEMPLATE_ID = "email_template_id";
    const EMAIL_TO = "email_to";
    const TICKET_PRIORITY = "ticket_priority";
    const TICKET_DEPARTMENT = "ticket_department";
    const CRON_EXPRESSION = "cron_expression";
    const WEBHOOK_URL = "webhook_url";
    const WEBHOOK_SECRET = "webhook_secret";
    const TIMEOUT_SECONDS = "timeout_seconds";
    const RETRY_COUNT = "retry_count";
    const RETRY_DELAY_MINUTES = "retry_delay_minutes";
    const SUCCESS_STATUS = "success_status";
    const FAILURE_STATUS = "failure_status";
    const IS_ACTIVE = "is_active";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::SERVICE_ID,
        self::PROCESSING_TYPE,
        self::API_ENDPOINT,
        self::API_METHOD,
        self::API_HEADERS,
        self::API_MAPPING,
        self::EMAIL_TEMPLATE_ID,
        self::EMAIL_TO,
        self::TICKET_PRIORITY,
        self::TICKET_DEPARTMENT,
        self::CRON_EXPRESSION,
        self::WEBHOOK_URL,
        self::WEBHOOK_SECRET,
        self::TIMEOUT_SECONDS,
        self::RETRY_COUNT,
        self::RETRY_DELAY_MINUTES,
        self::SUCCESS_STATUS,
        self::FAILURE_STATUS,
        self::IS_ACTIVE,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}
