<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SocialLoginAttempt extends BaseModel
{
    
    protected $table = "social_login_attempts";
    
    const PROVIDER_ID = "provider_id";
    const EMAIL = "email";
    const IP_ADDRESS = "ip_address";
    const USER_AGENT = "user_agent";
    const STATUS = "status";
    const ERROR_MESSAGE = "error_message";
    protected $fillable = [
        self::PROVIDER_ID,
        self::EMAIL,
        self::IP_ADDRESS,
        self::USER_AGENT,
        self::STATUS,
        self::ERROR_MESSAGE,
    ];
}