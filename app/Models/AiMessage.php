<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AiMessage extends BaseModel
{
    
    protected $table = "ai_messages";
    
    const CONVERSATION_ID = "conversation_id";
    const MESSAGE_UUID = "message_uuid";
    const ROLE = "role";
    const CONTENT = "content";
    const FUNCTION_NAME = "function_name";
    const FUNCTION_ARGUMENTS = "function_arguments";
    const FUNCTION_RESPONSE = "function_response";
    const MODEL_ID = "model_id";
    const INPUT_TOKENS = "input_tokens";
    const OUTPUT_TOKENS = "output_tokens";
    const TOTAL_TOKENS = "total_tokens";
    const COST = "cost";
    const RESPONSE_TIME_MS = "response_time_ms";
    const FINISH_REASON = "finish_reason";
    const METADATA = "metadata";
    const RAW_REQUEST = "raw_request";
    const RAW_RESPONSE = "raw_response";
    const USER_RATING = "user_rating";
    const USER_FEEDBACK = "user_feedback";
    protected $fillable = [
        self::CONVERSATION_ID,
        self::MESSAGE_UUID,
        self::ROLE,
        self::CONTENT,
        self::FUNCTION_NAME,
        self::FUNCTION_ARGUMENTS,
        self::FUNCTION_RESPONSE,
        self::MODEL_ID,
        self::INPUT_TOKENS,
        self::OUTPUT_TOKENS,
        self::TOTAL_TOKENS,
        self::COST,
        self::RESPONSE_TIME_MS,
        self::FINISH_REASON,
        self::METADATA,
        self::RAW_REQUEST,
        self::RAW_RESPONSE,
        self::USER_RATING,
        self::USER_FEEDBACK,
    ];
}