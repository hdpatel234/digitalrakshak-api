<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportConfig extends BaseModel
{
    protected $table = "client_support_configs";
    
    const SUPPORT_PLATFORM_ID = "support_platform_id";
    const CONFIG_NAME = "config_name";
    const IS_DEFAULT = "is_default";
    const API_URL = "api_url";
    const API_KEY = "api_key";
    const API_SECRET = "api_secret";
    const API_TOKEN = "api_token";
    const WEBHOOK_SECRET = "webhook_secret";
    const ADDITIONAL_CONFIG = "additional_config";
    const DEFAULT_PRIORITY = "default_priority";
    const DEFAULT_DEPARTMENT = "default_department";
    const DEFAULT_ASSIGNEE = "default_assignee";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::SUPPORT_PLATFORM_ID,
        self::CONFIG_NAME,
        self::IS_DEFAULT,
        self::API_URL,
        self::API_KEY,
        self::API_SECRET,
        self::API_TOKEN,
        self::WEBHOOK_SECRET,
        self::ADDITIONAL_CONFIG,
        self::DEFAULT_PRIORITY,
        self::DEFAULT_DEPARTMENT,
        self::DEFAULT_ASSIGNEE,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];

    protected $casts = [
        self::ADDITIONAL_CONFIG => 'array',
        self::IS_DEFAULT => 'boolean',
    ];

    public function supportPlatform(): BelongsTo
    {
        return $this->belongsTo(SupportPlatform::class, self::SUPPORT_PLATFORM_ID);
    }
}
