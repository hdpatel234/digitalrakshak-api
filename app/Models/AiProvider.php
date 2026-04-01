<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AiProvider extends BaseModel
{
    
    protected $table = "ai_providers";
    
    const PROVIDER_NAME = "provider_name";
    const PROVIDER_CODE = "provider_code";
    const PROVIDER_TYPE = "provider_type";
    const DESCRIPTION = "description";
    const WEBSITE = "website";
    const DOCUMENTATION_URL = "documentation_url";
    const ICON = "icon";
    const IS_ACTIVE = "is_active";
    const DISPLAY_ORDER = "display_order";
    protected $fillable = [
        self::PROVIDER_NAME,
        self::PROVIDER_CODE,
        self::PROVIDER_TYPE,
        self::DESCRIPTION,
        self::WEBSITE,
        self::DOCUMENTATION_URL,
        self::ICON,
        self::IS_ACTIVE,
        self::DISPLAY_ORDER,
    ];
}