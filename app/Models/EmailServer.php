<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class EmailServer extends BaseModel
{
    
    protected $table = "email_servers";
    
    const SERVER_NAME = "server_name";
    const SERVER_TYPE_ID = "server_type_id";
    const IS_DEFAULT = "is_default";
    const PRIORITY = "priority";
    const HOST = "host";
    const PORT = "port";
    const ENCRYPTION = "encryption";
    const USERNAME = "username";
    const PASSWORD = "password";
    const TIMEOUT = "timeout";
    const VERIFY_SSL = "verify_ssl";
    const AUTH_TYPE = "auth_type";
    const API_KEY = "api_key";
    const API_SECRET = "api_secret";
    const API_ENDPOINT = "api_endpoint";
    const DOMAIN = "domain";
    const RATE_LIMIT_PER_MINUTE = "rate_limit_per_minute";
    const RATE_LIMIT_PER_HOUR = "rate_limit_per_hour";
    const RATE_LIMIT_PER_DAY = "rate_limit_per_day";
    const DEFAULT_FROM_EMAIL = "default_from_email";
    const DEFAULT_FROM_NAME = "default_from_name";
    const DEFAULT_REPLY_TO = "default_reply_to";
    const SERVER_GROUP = "server_group";
    const WEIGHT = "weight";
    const STATUS = "status";
    const HEALTH_CHECK_AT = "health_check_at";
    const HEALTH_CHECK_STATUS = "health_check_status";
    const LAST_ERROR = "last_error";
    const SUCCESS_COUNT = "success_count";
    const FAILURE_COUNT = "failure_count";
    const LAST_USED_AT = "last_used_at";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::SERVER_NAME,
        self::SERVER_TYPE_ID,
        self::IS_DEFAULT,
        self::PRIORITY,
        self::HOST,
        self::PORT,
        self::ENCRYPTION,
        self::USERNAME,
        self::PASSWORD,
        self::TIMEOUT,
        self::VERIFY_SSL,
        self::AUTH_TYPE,
        self::API_KEY,
        self::API_SECRET,
        self::API_ENDPOINT,
        self::DOMAIN,
        self::RATE_LIMIT_PER_MINUTE,
        self::RATE_LIMIT_PER_HOUR,
        self::RATE_LIMIT_PER_DAY,
        self::DEFAULT_FROM_EMAIL,
        self::DEFAULT_FROM_NAME,
        self::DEFAULT_REPLY_TO,
        self::SERVER_GROUP,
        self::WEIGHT,
        self::STATUS,
        self::HEALTH_CHECK_AT,
        self::HEALTH_CHECK_STATUS,
        self::LAST_ERROR,
        self::SUCCESS_COUNT,
        self::FAILURE_COUNT,
        self::LAST_USED_AT,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}