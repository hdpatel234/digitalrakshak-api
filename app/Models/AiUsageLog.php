<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AiUsageLog extends BaseModel
{
    
    protected $table = "ai_usage_logs";
    
    const CONFIG_ID = "config_id";
    const MODEL_ID = "model_id";
    const USER_ID = "user_id";
    const CLIENT_ID = "client_id";
    const CONVERSATION_ID = "conversation_id";
    const MESSAGE_ID = "message_id";
    const INPUT_TOKENS = "input_tokens";
    const OUTPUT_TOKENS = "output_tokens";
    const TOTAL_TOKENS = "total_tokens";
    const COST = "cost";
    const REQUEST_TYPE = "request_type";
    const RESPONSE_TIME_MS = "response_time_ms";
    const SUCCESS = "success";
    const ERROR_MESSAGE = "error_message";
    protected $fillable = [
        self::CONFIG_ID,
        self::MODEL_ID,
        self::USER_ID,
        self::CLIENT_ID,
        self::CONVERSATION_ID,
        self::MESSAGE_ID,
        self::INPUT_TOKENS,
        self::OUTPUT_TOKENS,
        self::TOTAL_TOKENS,
        self::COST,
        self::REQUEST_TYPE,
        self::RESPONSE_TIME_MS,
        self::SUCCESS,
        self::ERROR_MESSAGE,
    ];
}