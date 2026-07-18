<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClientApiLog extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "client_api_logs";
    
    const CLIENT_ID = "client_id";
    const API_KEY_ID = "api_key_id";
    const ENDPOINT = "endpoint";
    const METHOD = "method";
    const REQUEST_HEADERS = "request_headers";
    const REQUEST_BODY = "request_body";
    const RESPONSE_CODE = "response_code";
    const RESPONSE_BODY = "response_body";
    const RESPONSE_TIME_MS = "response_time_ms";
    const IP_ADDRESS = "ip_address";
    const USER_AGENT = "user_agent";
    const STATUS = "status";
    const ERROR_MESSAGE = "error_message";
    protected $fillable = [
        self::CLIENT_ID,
        self::API_KEY_ID,
        self::ENDPOINT,
        self::METHOD,
        self::REQUEST_HEADERS,
        self::REQUEST_BODY,
        self::RESPONSE_CODE,
        self::RESPONSE_BODY,
        self::RESPONSE_TIME_MS,
        self::IP_ADDRESS,
        self::USER_AGENT,
        self::STATUS,
        self::ERROR_MESSAGE,
    ];
}