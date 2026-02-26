<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ApiProviderLog extends BaseModel
{
    
    protected $table = "api_provider_logs";
    
    const API_PROVIDER_ID = "api_provider_id";
    const ENDPOINT = "endpoint";
    const METHOD = "method";
    const REQUEST = "request";
    const RESPONSE = "response";
    const RESPONSE_CODE = "response_code";
    const DURATION = "duration";
    const IS_SUCCESSFUL = "is_successful";
    protected $fillable = [
        self::API_PROVIDER_ID,
        self::ENDPOINT,
        self::METHOD,
        self::REQUEST,
        self::RESPONSE,
        self::RESPONSE_CODE,
        self::DURATION,
        self::IS_SUCCESSFUL,
    ];
}