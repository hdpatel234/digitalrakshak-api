<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ClientApiKey extends BaseModel
{
    
    protected $table = "client_api_keys";
    
    const CLIENT_ID = "client_id";
    const KEY_NAME = "key_name";
    const API_KEY = "api_key";
    const API_SECRET = "api_secret";
    const KEY_TYPE = "key_type";
    const PERMISSIONS = "permissions";
    const IP_WHITELIST = "ip_whitelist";
    const RATE_LIMIT = "rate_limit";
    const RATE_LIMIT_PER_DAY = "rate_limit_per_day";
    const EXPIRES_AT = "expires_at";
    const LAST_USED_AT = "last_used_at";
    const LAST_USED_IP = "last_used_ip";
    const TOTAL_REQUESTS = "total_requests";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::KEY_NAME,
        self::API_KEY,
        self::API_SECRET,
        self::KEY_TYPE,
        self::PERMISSIONS,
        self::IP_WHITELIST,
        self::RATE_LIMIT,
        self::RATE_LIMIT_PER_DAY,
        self::EXPIRES_AT,
        self::LAST_USED_AT,
        self::LAST_USED_IP,
        self::TOTAL_REQUESTS,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}