<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmailServerHealth extends BaseModel
{
    
    protected $table = "email_server_health";
    
    const SERVER_ID = "server_id";
    const CHECK_TYPE = "check_type";
    const STATUS = "status";
    const RESPONSE_TIME_MS = "response_time_ms";
    const ERROR_MESSAGE = "error_message";
    const CHECKED_AT = "checked_at";
    protected $fillable = [
        self::SERVER_ID,
        self::CHECK_TYPE,
        self::STATUS,
        self::RESPONSE_TIME_MS,
        self::ERROR_MESSAGE,
        self::CHECKED_AT,
    ];
}