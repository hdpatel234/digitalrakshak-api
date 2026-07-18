<?php

namespace App\Repositories;

use App\Models\ProviderApiConfig;

class ProviderApiConfigRepository extends BaseRepository
{
    public function __construct(ProviderApiConfig $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function providerId()
    {
        return ProviderApiConfig::PROVIDER_ID;
    }

    public function configName()
    {
        return ProviderApiConfig::CONFIG_NAME;
    }

    public function environment()
    {
        return ProviderApiConfig::ENVIRONMENT;
    }

    public function status()
    {
        return ProviderApiConfig::STATUS;
    }

    public function baseUrl()
    {
        return ProviderApiConfig::BASE_URL;
    }

    public function apiVersion()
    {
        return ProviderApiConfig::API_VERSION;
    }

    public function timeoutSeconds()
    {
        return ProviderApiConfig::TIMEOUT_SECONDS;
    }

    public function maxRetries()
    {
        return ProviderApiConfig::MAX_RETRIES;
    }

    public function retryDelaySeconds()
    {
        return ProviderApiConfig::RETRY_DELAY_SECONDS;
    }

    public function authType()
    {
        return ProviderApiConfig::AUTH_TYPE;
    }

    public function apiKey()
    {
        return ProviderApiConfig::API_KEY;
    }

    public function apiSecret()
    {
        return ProviderApiConfig::API_SECRET;
    }

    public function apiToken()
    {
        return ProviderApiConfig::API_TOKEN;
    }

    public function publicKey()
    {
        return ProviderApiConfig::PUBLIC_KEY;
    }

    public function privateKey()
    {
        return ProviderApiConfig::PRIVATE_KEY;
    }

    public function tokenExpiry()
    {
        return ProviderApiConfig::TOKEN_EXPIRY;
    }

    public function username()
    {
        return ProviderApiConfig::USERNAME;
    }

    public function password()
    {
        return ProviderApiConfig::PASSWORD;
    }

    public function defaultHeaders()
    {
        return ProviderApiConfig::DEFAULT_HEADERS;
    }

    public function dynamicHeaders()
    {
        return ProviderApiConfig::DYNAMIC_HEADERS;
    }

    public function rateLimitPerMinute()
    {
        return ProviderApiConfig::RATE_LIMIT_PER_MINUTE;
    }

    public function rateLimitPerHour()
    {
        return ProviderApiConfig::RATE_LIMIT_PER_HOUR;
    }

    public function rateLimitPerDay()
    {
        return ProviderApiConfig::RATE_LIMIT_PER_DAY;
    }

    public function verifySsl()
    {
        return ProviderApiConfig::VERIFY_SSL;
    }

    public function sslCertPath()
    {
        return ProviderApiConfig::SSL_CERT_PATH;
    }

    public function sslKeyPath()
    {
        return ProviderApiConfig::SSL_KEY_PATH;
    }

    public function healthCheckUrl()
    {
        return ProviderApiConfig::HEALTH_CHECK_URL;
    }

    public function healthCheckInterval()
    {
        return ProviderApiConfig::HEALTH_CHECK_INTERVAL;
    }

    public function lastHealthCheck()
    {
        return ProviderApiConfig::LAST_HEALTH_CHECK;
    }

    public function healthStatus()
    {
        return ProviderApiConfig::HEALTH_STATUS;
    }

    public function healthMessage()
    {
        return ProviderApiConfig::HEALTH_MESSAGE;
    }

    public function avgResponseTime()
    {
        return ProviderApiConfig::AVG_RESPONSE_TIME;
    }

    public function successRate()
    {
        return ProviderApiConfig::SUCCESS_RATE;
    }

    public function totalCalls()
    {
        return ProviderApiConfig::TOTAL_CALLS;
    }

    public function successfulCalls()
    {
        return ProviderApiConfig::SUCCESSFUL_CALLS;
    }

    public function failedCalls()
    {
        return ProviderApiConfig::FAILED_CALLS;
    }

    public function createdBy()
    {
        return ProviderApiConfig::CREATED_BY;
    }

    public function updatedBy()
    {
        return ProviderApiConfig::UPDATED_BY;
    }
    // functions
}