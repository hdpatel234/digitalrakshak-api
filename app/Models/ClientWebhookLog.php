<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClientWebhookLog extends BaseModel
{
    
    protected $table = "client_webhook_logs";
    
    const CLIENT_ID = "client_id";
    const WEBHOOK_ID = "webhook_id";
    const EVENT_TYPE = "event_type";
    const PAYLOAD = "payload";
    const HEADERS = "headers";
    const RESPONSE_CODE = "response_code";
    const RESPONSE_BODY = "response_body";
    const RESPONSE_TIME_MS = "response_time_ms";
    const ATTEMPT = "attempt";
    const STATUS = "status";
    const ERROR_MESSAGE = "error_message";
    const NEXT_RETRY_AT = "next_retry_at";
    protected $fillable = [
        self::CLIENT_ID,
        self::WEBHOOK_ID,
        self::EVENT_TYPE,
        self::PAYLOAD,
        self::HEADERS,
        self::RESPONSE_CODE,
        self::RESPONSE_BODY,
        self::RESPONSE_TIME_MS,
        self::ATTEMPT,
        self::STATUS,
        self::ERROR_MESSAGE,
        self::NEXT_RETRY_AT,
    ];
}