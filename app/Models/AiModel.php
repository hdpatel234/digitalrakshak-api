<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AiModel extends BaseModel
{
    
    protected $table = "ai_models";
    
    const PROVIDER_ID = "provider_id";
    const MODEL_NAME = "model_name";
    const MODEL_CODE = "model_code";
    const MODEL_TYPE = "model_type";
    const DESCRIPTION = "description";
    const MAX_TOKENS = "max_tokens";
    const MAX_INPUT_TOKENS = "max_input_tokens";
    const MAX_OUTPUT_TOKENS = "max_output_tokens";
    const SUPPORTS_FUNCTIONS = "supports_functions";
    const SUPPORTS_VISION = "supports_vision";
    const SUPPORTS_STREAMING = "supports_streaming";
    const SUPPORTS_JSON_MODE = "supports_json_mode";
    const INPUT_COST_PER_1K = "input_cost_per_1k";
    const OUTPUT_COST_PER_1K = "output_cost_per_1k";
    const CURRENCY = "currency";
    const IS_ACTIVE = "is_active";
    const IS_DEFAULT = "is_default";
    const DISPLAY_ORDER = "display_order";
    const CAPABILITIES = "capabilities";
    protected $fillable = [
        self::PROVIDER_ID,
        self::MODEL_NAME,
        self::MODEL_CODE,
        self::MODEL_TYPE,
        self::DESCRIPTION,
        self::MAX_TOKENS,
        self::MAX_INPUT_TOKENS,
        self::MAX_OUTPUT_TOKENS,
        self::SUPPORTS_FUNCTIONS,
        self::SUPPORTS_VISION,
        self::SUPPORTS_STREAMING,
        self::SUPPORTS_JSON_MODE,
        self::INPUT_COST_PER_1K,
        self::OUTPUT_COST_PER_1K,
        self::CURRENCY,
        self::IS_ACTIVE,
        self::IS_DEFAULT,
        self::DISPLAY_ORDER,
        self::CAPABILITIES,
    ];
}