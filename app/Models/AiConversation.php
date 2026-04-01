<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AiConversation extends BaseModel
{
    
    protected $table = "ai_conversations";
    
    const CONVERSATION_UUID = "conversation_uuid";
    const USER_ID = "user_id";
    const CLIENT_ID = "client_id";
    const CONFIG_ID = "config_id";
    const MODEL_ID = "model_id";
    const PROMPT_ID = "prompt_id";
    const TITLE = "title";
    const CONTEXT = "context";
    const TOTAL_TOKENS = "total_tokens";
    const TOTAL_COST = "total_cost";
    const STATUS = "status";
    const LAST_MESSAGE_AT = "last_message_at";
    protected $fillable = [
        self::CONVERSATION_UUID,
        self::USER_ID,
        self::CLIENT_ID,
        self::CONFIG_ID,
        self::MODEL_ID,
        self::PROMPT_ID,
        self::TITLE,
        self::CONTEXT,
        self::TOTAL_TOKENS,
        self::TOTAL_COST,
        self::STATUS,
        self::LAST_MESSAGE_AT,
    ];
}