<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserConfigCategory extends BaseModel
{
    
    protected $table = "user_config_categories";
    
    const CATEGORY_NAME = "category_name";
    const CATEGORY_CODE = "category_code";
    const DESCRIPTION = "description";
    const DISPLAY_ORDER = "display_order";
    const ICON = "icon";
    const IS_SYSTEM = "is_system";
    const IS_ACTIVE = "is_active";
    protected $fillable = [
        self::CATEGORY_NAME,
        self::CATEGORY_CODE,
        self::DESCRIPTION,
        self::DISPLAY_ORDER,
        self::ICON,
        self::IS_SYSTEM,
        self::IS_ACTIVE,
    ];
}