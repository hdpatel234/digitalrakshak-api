<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProviderAssignment extends BaseModel
{
    
    protected $table = "service_provider_assignments";
    
    const SERVICE_ID = "service_id";
    const PROVIDER_ID = "provider_id";
    const PROVIDER_CONFIG_ID = "provider_config_id";
    const PRIORITY = "priority";
    const IS_ACTIVE = "is_active";
    const IS_DEFAULT = "is_default";
    const IS_PRIMARY = "is_primary";
    const IS_BACKUP = "is_backup";
    const FALLBACK_THRESHOLD = "fallback_threshold";
    const COOLDOWN_PERIOD = "cooldown_period";
    const ENDPOINT_OVERRIDE = "endpoint_override";
    const METHOD_OVERRIDE = "method_override";
    const HEADERS_OVERRIDE = "headers_override";
    const BODY_TEMPLATE = "body_template";
    const CURRENT_STATUS = "current_status";
    const FAILURE_COUNT = "failure_count";
    const LAST_FAILURE_AT = "last_failure_at";
    const LAST_SUCCESS_AT = "last_success_at";
    const LAST_USED_AT = "last_used_at";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::SERVICE_ID,
        self::PROVIDER_ID,
        self::PROVIDER_CONFIG_ID,
        self::PRIORITY,
        self::IS_ACTIVE,
        self::IS_DEFAULT,
        self::IS_PRIMARY,
        self::IS_BACKUP,
        self::FALLBACK_THRESHOLD,
        self::COOLDOWN_PERIOD,
        self::ENDPOINT_OVERRIDE,
        self::METHOD_OVERRIDE,
        self::HEADERS_OVERRIDE,
        self::BODY_TEMPLATE,
        self::CURRENT_STATUS,
        self::FAILURE_COUNT,
        self::LAST_FAILURE_AT,
        self::LAST_SUCCESS_AT,
        self::LAST_USED_AT,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}