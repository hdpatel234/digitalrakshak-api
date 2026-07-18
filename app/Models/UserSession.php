<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class UserSession extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "user_sessions";
    
    const USER_ID = "user_id";
    const ACCESS_TOKEN_ID = "access_token_id";
    const IP_ADDRESS = "ip_address";
    const USER_AGENT = "user_agent";
    const BROWSER = "browser";
    const OS = "os";
    const DEVICE = "device";
    const IS_ACTIVE = "is_active";
    protected $fillable = [
        self::USER_ID,
        self::ACCESS_TOKEN_ID,
        self::IP_ADDRESS,
        self::USER_AGENT,
        self::BROWSER,
        self::OS,
        self::DEVICE,
        self::IS_ACTIVE,
    ];
}