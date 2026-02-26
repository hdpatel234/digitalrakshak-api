<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ApiLog extends BaseModel
{
    
    protected $table = "api_logs";
    
    const SERVICE_ID = "service_id";
    const ORDER_ITEM_ID = "order_item_id";
    const ENDPOINT = "endpoint";
    const METHOD = "method";
    const REQUEST_DATA = "request_data";
    const RESPONSE_DATA = "response_data";
    const HTTP_STATUS = "http_status";
    const STATUS = "status";
    const ERROR_MESSAGE = "error_message";
    const DURATION_MS = "duration_ms";
    const IP_ADDRESS = "ip_address";
    protected $fillable = [
        self::SERVICE_ID,
        self::ORDER_ITEM_ID,
        self::ENDPOINT,
        self::METHOD,
        self::REQUEST_DATA,
        self::RESPONSE_DATA,
        self::HTTP_STATUS,
        self::STATUS,
        self::ERROR_MESSAGE,
        self::DURATION_MS,
        self::IP_ADDRESS,
    ];
}