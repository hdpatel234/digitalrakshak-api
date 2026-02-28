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
        return $this->model::PROVIDER_ID;
    }

    public function configName()
    {
        return $this->model::CONFIG_NAME;
    }

    public function environment()
    {
        return $this->model::ENVIRONMENT;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function isDefault()
    {
        return $this->model::IS_DEFAULT;
    }

    public function baseUrl()
    {
        return $this->model::BASE_URL;
    }

    public function apiVersion()
    {
        return $this->model::API_VERSION;
    }

    public function timeoutSeconds()
    {
        return $this->model::TIMEOUT_SECONDS;
    }

    public function maxRetries()
    {
        return $this->model::MAX_RETRIES;
    }

    public function retryDelaySeconds()
    {
        return $this->model::RETRY_DELAY_SECONDS;
    }

    public function authType()
    {
        return $this->model::AUTH_TYPE;
    }

    public function apiKey()
    {
        return $this->model::API_KEY;
    }

    public function apiSecret()
    {
        return $this->model::API_SECRET;
    }

    public function apiToken()
    {
        return $this->model::API_TOKEN;
    }

    public function tokenExpiry()
    {
        return $this->model::TOKEN_EXPIRY;
    }

    public function username()
    {
        return $this->model::USERNAME;
    }

    public function password()
    {
        return $this->model::PASSWORD;
    }

    public function defaultHeaders()
    {
        return $this->model::DEFAULT_HEADERS;
    }

    public function dynamicHeaders()
    {
        return $this->model::DYNAMIC_HEADERS;
    }

    public function rateLimitPerMinute()
    {
        return $this->model::RATE_LIMIT_PER_MINUTE;
    }

    public function rateLimitPerHour()
    {
        return $this->model::RATE_LIMIT_PER_HOUR;
    }

    public function rateLimitPerDay()
    {
        return $this->model::RATE_LIMIT_PER_DAY;
    }

    public function verifySsl()
    {
        return $this->model::VERIFY_SSL;
    }

    public function sslCertPath()
    {
        return $this->model::SSL_CERT_PATH;
    }

    public function sslKeyPath()
    {
        return $this->model::SSL_KEY_PATH;
    }

    public function healthCheckUrl()
    {
        return $this->model::HEALTH_CHECK_URL;
    }

    public function healthCheckInterval()
    {
        return $this->model::HEALTH_CHECK_INTERVAL;
    }

    public function lastHealthCheck()
    {
        return $this->model::LAST_HEALTH_CHECK;
    }

    public function healthStatus()
    {
        return $this->model::HEALTH_STATUS;
    }

    public function healthMessage()
    {
        return $this->model::HEALTH_MESSAGE;
    }

    public function avgResponseTime()
    {
        return $this->model::AVG_RESPONSE_TIME;
    }

    public function successRate()
    {
        return $this->model::SUCCESS_RATE;
    }

    public function totalCalls()
    {
        return $this->model::TOTAL_CALLS;
    }

    public function successfulCalls()
    {
        return $this->model::SUCCESSFUL_CALLS;
    }

    public function failedCalls()
    {
        return $this->model::FAILED_CALLS;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }
    // functions
}