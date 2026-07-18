<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SocialLoginProvider extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "social_login_providers";
    
    const PROVIDER_NAME = "provider_name";
    const PROVIDER_CODE = "provider_code";
    const ICON = "icon";
    const COLOR = "color";
    const DESCRIPTION = "description";
    const CLIENT_ID = "client_id";
    const CLIENT_SECRET = "client_secret";
    const REDIRECT_URL = "redirect_url";
    const SCOPES = "scopes";
    const AUTH_PARAMETERS = "auth_parameters";
    const BUTTON_TEXT = "button_text";
    const BUTTON_ICON = "button_icon";
    const BUTTON_COLOR = "button_color";
    const DISPLAY_ORDER = "display_order";
    const IS_ENABLED = "is_enabled";
    const IS_DEFAULT = "is_default";
    const TOTAL_USERS = "total_users";
    const TOTAL_CONNECTIONS = "total_connections";
    const LAST_USED_AT = "last_used_at";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::PROVIDER_NAME,
        self::PROVIDER_CODE,
        self::ICON,
        self::COLOR,
        self::DESCRIPTION,
        self::CLIENT_ID,
        self::CLIENT_SECRET,
        self::REDIRECT_URL,
        self::SCOPES,
        self::AUTH_PARAMETERS,
        self::BUTTON_TEXT,
        self::BUTTON_ICON,
        self::BUTTON_COLOR,
        self::DISPLAY_ORDER,
        self::IS_ENABLED,
        self::IS_DEFAULT,
        self::TOTAL_USERS,
        self::TOTAL_CONNECTIONS,
        self::LAST_USED_AT,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}