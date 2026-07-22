<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClientWebhook extends BaseModel
{
    use SoftDeletes;
    protected $table = "client_webhooks";
    const CLIENT_ID = "client_id";
    const WEBHOOK_NAME = "webhook_name";
    const WEBHOOK_URL = "webhook_url";
    const WEBHOOK_SECRET = "webhook_secret";
    const EVENTS = "events";
    const HEADERS = "headers";
    const FORMAT = "format";
    const MAX_RETRIES = "max_retries";
    const RETRY_DELAY_SECONDS = "retry_delay_seconds";
    const TIMEOUT_SECONDS = "timeout_seconds";
    const LAST_TRIGGERED_AT = "last_triggered_at";
    const LAST_SUCCESS_AT = "last_success_at";
    const LAST_FAILURE_AT = "last_failure_at";
    const LAST_ERROR = "last_error";
    const TOTAL_SUCCESS = "total_success";
    const TOTAL_FAILURES = "total_failures";
    protected $fillable = [
        self::CLIENT_ID,
        self::WEBHOOK_NAME,
        self::WEBHOOK_URL,
        self::WEBHOOK_SECRET,
        self::EVENTS,
        self::HEADERS,
        self::FORMAT,
        self::MAX_RETRIES,
        self::RETRY_DELAY_SECONDS,
        self::TIMEOUT_SECONDS,
        self::LAST_TRIGGERED_AT,
        self::LAST_SUCCESS_AT,
        self::LAST_FAILURE_AT,
        self::LAST_ERROR,
        self::TOTAL_SUCCESS,
        self::TOTAL_FAILURES,
        self::STATUS
    ];
}
