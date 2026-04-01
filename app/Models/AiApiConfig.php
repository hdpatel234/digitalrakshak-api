<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AiApiConfig extends BaseModel
{
    
    protected $table = "ai_api_configs";
    
    const CONFIG_NAME = "config_name";
    const PROVIDER_ID = "provider_id";
    const MODEL_ID = "model_id";
    const API_KEY = "api_key";
    const API_SECRET = "api_secret";
    const ORGANIZATION_ID = "organization_id";
    const PROJECT_ID = "project_id";
    const BASE_URL = "base_url";
    const DEFAULT_MODEL = "default_model";
    const DEFAULT_TEMPERATURE = "default_temperature";
    const DEFAULT_MAX_TOKENS = "default_max_tokens";
    const DEFAULT_TOP_P = "default_top_p";
    const DEFAULT_FREQUENCY_PENALTY = "default_frequency_penalty";
    const DEFAULT_PRESENCE_PENALTY = "default_presence_penalty";
    const REQUESTS_PER_MINUTE = "requests_per_minute";
    const TOKENS_PER_MINUTE = "tokens_per_minute";
    const ENABLE_STREAMING = "enable_streaming";
    const ENABLE_FUNCTIONS = "enable_functions";
    const ENABLE_VISION = "enable_vision";
    const ENVIRONMENT = "environment";
    const IS_ACTIVE = "is_active";
    const IS_DEFAULT = "is_default";
    const TOTAL_REQUESTS = "total_requests";
    const TOTAL_TOKENS = "total_tokens";
    const TOTAL_COST = "total_cost";
    const LAST_USED_AT = "last_used_at";
    const HEALTH_STATUS = "health_status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::CONFIG_NAME,
        self::PROVIDER_ID,
        self::MODEL_ID,
        self::API_KEY,
        self::API_SECRET,
        self::ORGANIZATION_ID,
        self::PROJECT_ID,
        self::BASE_URL,
        self::DEFAULT_MODEL,
        self::DEFAULT_TEMPERATURE,
        self::DEFAULT_MAX_TOKENS,
        self::DEFAULT_TOP_P,
        self::DEFAULT_FREQUENCY_PENALTY,
        self::DEFAULT_PRESENCE_PENALTY,
        self::REQUESTS_PER_MINUTE,
        self::TOKENS_PER_MINUTE,
        self::ENABLE_STREAMING,
        self::ENABLE_FUNCTIONS,
        self::ENABLE_VISION,
        self::ENVIRONMENT,
        self::IS_ACTIVE,
        self::IS_DEFAULT,
        self::TOTAL_REQUESTS,
        self::TOTAL_TOKENS,
        self::TOTAL_COST,
        self::LAST_USED_AT,
        self::HEALTH_STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}