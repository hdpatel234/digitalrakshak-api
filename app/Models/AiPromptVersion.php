<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AiPromptVersion extends BaseModel
{
    
    protected $table = "ai_prompt_versions";
    
    const PROMPT_ID = "prompt_id";
    const VERSION = "version";
    const SYSTEM_PROMPT = "system_prompt";
    const USER_PROMPT_TEMPLATE = "user_prompt_template";
    const EXAMPLES = "examples";
    const TEMPERATURE = "temperature";
    const MAX_TOKENS = "max_tokens";
    const CREATED_BY = "created_by";
    protected $fillable = [
        self::PROMPT_ID,
        self::VERSION,
        self::SYSTEM_PROMPT,
        self::USER_PROMPT_TEMPLATE,
        self::EXAMPLES,
        self::TEMPERATURE,
        self::MAX_TOKENS,
        self::CREATED_BY,
    ];
}