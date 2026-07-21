<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProvider extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "service_providers";
    
    const PROVIDER_NAME = "provider_name";
    const PROVIDER_CODE = "provider_code";
    const PROVIDER_TYPE = "provider_type";
    const LOGO = "logo";
    const DESCRIPTION = "description";
    const WEBSITE = "website";
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
        self::LOGO,
        self::DESCRIPTION,
        self::WEBSITE,
        self::DOCUMENTATION_URL,
        self::STATUS,
        self::IS_DEFAULT,
        self::PRIORITY,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}
