<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserSocialConnection extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "user_social_connections";
    
    const USER_ID = "user_id";
    const PROVIDER_ID = "provider_id";
    const PROVIDER_USER_ID = "provider_user_id";
    const PROVIDER_EMAIL = "provider_email";
    const PROVIDER_NAME = "provider_name";
    const PROVIDER_AVATAR = "provider_avatar";
    const ACCESS_TOKEN = "access_token";
    const REFRESH_TOKEN = "refresh_token";
    const TOKEN_EXPIRES_AT = "token_expires_at";
    const SCOPES = "scopes";
    const RAW_DATA = "raw_data";
    const LAST_LOGIN_AT = "last_login_at";
    const IS_ACTIVE = "is_active";
    protected $fillable = [
        self::USER_ID,
        self::PROVIDER_ID,
        self::PROVIDER_USER_ID,
        self::PROVIDER_EMAIL,
        self::PROVIDER_NAME,
        self::PROVIDER_AVATAR,
        self::ACCESS_TOKEN,
        self::REFRESH_TOKEN,
        self::TOKEN_EXPIRES_AT,
        self::SCOPES,
        self::RAW_DATA,
        self::LAST_LOGIN_AT,
        self::IS_ACTIVE,
    ];
}
