<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserTheme extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "user_themes";
    
    const THEME_NAME = "theme_name";
    const THEME_CODE = "theme_code";
    const IS_DEFAULT = "is_default";
    const IS_SYSTEM = "is_system";
    const COLORS = "colors";
    const FONTS = "fonts";
    const BORDER_RADIUS = "border_radius";
    const SPACING = "spacing";
    const ANIMATIONS = "animations";
    const BACKGROUND_IMAGE = "background_image";
    const CUSTOM_CSS = "custom_css";
    const CREATED_BY = "created_by";
    protected $fillable = [
        self::THEME_NAME,
        self::THEME_CODE,
        self::IS_DEFAULT,
        self::IS_SYSTEM,
        self::COLORS,
        self::FONTS,
        self::BORDER_RADIUS,
        self::SPACING,
        self::ANIMATIONS,
        self::BACKGROUND_IMAGE,
        self::CUSTOM_CSS,
        self::CREATED_BY,
    ];
}