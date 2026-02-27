<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserShortcut extends BaseModel
{
    
    protected $table = "user_shortcuts";
    
    const USER_ID = "user_id";
    const ACTION = "action";
    const SHORTCUT = "shortcut";
    const SCOPE = "scope";
    const IS_ENABLED = "is_enabled";
    protected $fillable = [
        self::USER_ID,
        self::ACTION,
        self::SHORTCUT,
        self::SCOPE,
        self::IS_ENABLED,
    ];
}