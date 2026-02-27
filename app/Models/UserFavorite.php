<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserFavorite extends BaseModel
{
    
    protected $table = "user_favorites";
    
    const USER_ID = "user_id";
    const FAVORITE_TYPE = "favorite_type";
    const FAVORITE_ID = "favorite_id";
    const URL = "url";
    const TITLE = "title";
    const ICON = "icon";
    const METADATA = "metadata";
    const DISPLAY_ORDER = "display_order";
    protected $fillable = [
        self::USER_ID,
        self::FAVORITE_TYPE,
        self::FAVORITE_ID,
        self::URL,
        self::TITLE,
        self::ICON,
        self::METADATA,
        self::DISPLAY_ORDER,
    ];
}