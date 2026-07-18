<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class WebhookLog extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "webhook_logs";
    
    const SOURCE = "source";
    const PLATFORM = "platform";
    const CLIENT_ID = "client_id";
    const EVENT_TYPE = "event_type";
    const PAYLOAD = "payload";
    const HEADERS = "headers";
    const PROCESSED = "processed";
    const PROCESSED_AT = "processed_at";
    const ERROR_MESSAGE = "error_message";
    protected $fillable = [
        self::SOURCE,
        self::PLATFORM,
        self::CLIENT_ID,
        self::EVENT_TYPE,
        self::PAYLOAD,
        self::HEADERS,
        self::PROCESSED,
        self::PROCESSED_AT,
        self::ERROR_MESSAGE,
    ];
}