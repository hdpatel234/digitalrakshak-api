<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserConfigCategory extends BaseModel
{
    use SoftDeletes;
    protected $table = "user_config_categories";
    const CATEGORY_NAME = "category_name";
    const CATEGORY_CODE = "category_code";
    const DISPLAY_ORDER = "display_order";
    const ICON = "icon";
    const IS_SYSTEM = "is_system";
    protected $fillable = [
        self::CATEGORY_NAME,
        self::CATEGORY_CODE,
        self::DISPLAY_ORDER,
        self::ICON,
        self::IS_SYSTEM
    ];
}
