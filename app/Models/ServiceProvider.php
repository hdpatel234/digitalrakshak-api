<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProvider extends BaseModel
{
    
    protected $table = "service_providers";
    
    const PROVIDER_NAME = "provider_name";
    const PROVIDER_CODE = "provider_code";
    const PROVIDER_TYPE = "provider_type";
    const DESCRIPTION = "description";
    const WEBSITE = "website";
    const SUPPORT_EMAIL = "support_email";
    const SUPPORT_PHONE = "support_phone";
    const DOCUMENTATION_URL = "documentation_url";
    const STATUS = "status";
    const IS_DEFAULT = "is_default";
    const PRIORITY = "priority";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::PROVIDER_NAME,
        self::PROVIDER_CODE,
        self::PROVIDER_TYPE,
        self::DESCRIPTION,
        self::WEBSITE,
        self::SUPPORT_EMAIL,
        self::SUPPORT_PHONE,
        self::DOCUMENTATION_URL,
        self::STATUS,
        self::IS_DEFAULT,
        self::PRIORITY,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}