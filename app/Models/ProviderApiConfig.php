<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderApiConfig extends BaseModel
{
    use SoftDeletes;
    
    protected $table = "provider_api_configs";
    
    const PROVIDER_ID = "provider_id";
    const CONFIG_NAME = "config_name";
    const ENVIRONMENT = "environment";
    const STATUS = "status";
    const BASE_URL = "base_url";
    const API_VERSION = "api_version";
    const TIMEOUT_SECONDS = "timeout_seconds";
    const MAX_RETRIES = "max_retries";
    const RETRY_DELAY_SECONDS = "retry_delay_seconds";
    const AUTH_TYPE = "auth_type";
    const API_KEY = "api_key";
    const API_SECRET = "api_secret";
    const API_TOKEN = "api_token";
    const TOKEN_EXPIRY = "token_expiry";
    const USERNAME = "username";
    const PASSWORD = "password";
    const DEFAULT_HEADERS = "default_headers";
    const DYNAMIC_HEADERS = "dynamic_headers";
    const RATE_LIMIT_PER_MINUTE = "rate_limit_per_minute";
    const RATE_LIMIT_PER_HOUR = "rate_limit_per_hour";
    const RATE_LIMIT_PER_DAY = "rate_limit_per_day";
    const VERIFY_SSL = "verify_ssl";
    const SSL_CERT_PATH = "ssl_cert_path";
    const SSL_KEY_PATH = "ssl_key_path";
    const HEALTH_CHECK_URL = "health_check_url";
    const HEALTH_CHECK_INTERVAL = "health_check_interval";
    const LAST_HEALTH_CHECK = "last_health_check";
    const HEALTH_STATUS = "health_status";
    const HEALTH_MESSAGE = "health_message";
    const AVG_RESPONSE_TIME = "avg_response_time";
    const SUCCESS_RATE = "success_rate";
    const TOTAL_CALLS = "total_calls";
    const SUCCESSFUL_CALLS = "successful_calls";
    const FAILED_CALLS = "failed_calls";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::PROVIDER_ID,
        self::CONFIG_NAME,
        self::ENVIRONMENT,
        self::STATUS,
        self::BASE_URL,
        self::API_VERSION,
        self::TIMEOUT_SECONDS,
        self::MAX_RETRIES,
        self::RETRY_DELAY_SECONDS,
        self::AUTH_TYPE,
        self::API_KEY,
        self::API_SECRET,
        self::API_TOKEN,
        self::TOKEN_EXPIRY,
        self::USERNAME,
        self::PASSWORD,
        self::DEFAULT_HEADERS,
        self::DYNAMIC_HEADERS,
        self::RATE_LIMIT_PER_MINUTE,
        self::RATE_LIMIT_PER_HOUR,
        self::RATE_LIMIT_PER_DAY,
        self::VERIFY_SSL,
        self::SSL_CERT_PATH,
        self::SSL_KEY_PATH,
        self::HEALTH_CHECK_URL,
        self::HEALTH_CHECK_INTERVAL,
        self::LAST_HEALTH_CHECK,
        self::HEALTH_STATUS,
        self::HEALTH_MESSAGE,
        self::AVG_RESPONSE_TIME,
        self::SUCCESS_RATE,
        self::TOTAL_CALLS,
        self::SUCCESSFUL_CALLS,
        self::FAILED_CALLS,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];

    protected $casts = [
        self::ENVIRONMENT => \App\Enums\EnvironmentEnum::class,
    ];
}