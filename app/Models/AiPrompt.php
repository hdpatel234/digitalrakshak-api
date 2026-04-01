<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AiPrompt extends BaseModel
{
    
    protected $table = "ai_prompts";
    
    const PROMPT_NAME = "prompt_name";
    const PROMPT_CODE = "prompt_code";
    const CATEGORY = "category";
    const DESCRIPTION = "description";
    const SYSTEM_PROMPT = "system_prompt";
    const USER_PROMPT_TEMPLATE = "user_prompt_template";
    const EXAMPLES = "examples";
    const PROVIDER_ID = "provider_id";
    const MODEL_ID = "model_id";
    const TEMPERATURE = "temperature";
    const MAX_TOKENS = "max_tokens";
    const RESPONSE_FORMAT = "response_format";
    const RESPONSE_SCHEMA = "response_schema";
    const PARSE_RESPONSE = "parse_response";
    const FUNCTIONS = "functions";
    const VERSION = "version";
    const IS_ACTIVE = "is_active";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::PROMPT_NAME,
        self::PROMPT_CODE,
        self::CATEGORY,
        self::DESCRIPTION,
        self::SYSTEM_PROMPT,
        self::USER_PROMPT_TEMPLATE,
        self::EXAMPLES,
        self::PROVIDER_ID,
        self::MODEL_ID,
        self::TEMPERATURE,
        self::MAX_TOKENS,
        self::RESPONSE_FORMAT,
        self::RESPONSE_SCHEMA,
        self::PARSE_RESPONSE,
        self::FUNCTIONS,
        self::VERSION,
        self::IS_ACTIVE,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}