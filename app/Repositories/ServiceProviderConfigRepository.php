<?php

namespace App\Repositories;

use App\Models\ServiceProviderConfig;

class ServiceProviderConfigRepository extends BaseRepository
{
    public function __construct(ServiceProviderConfig $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function providerId()
    {
        return ServiceProviderConfig::PROVIDER_ID;
    }

    public function configName()
    {
        return ServiceProviderConfig::CONFIG_NAME;
    }

    public function environment()
    {
        return ServiceProviderConfig::ENVIRONMENT;
    }

    public function status()
    {
        return ServiceProviderConfig::STATUS;
    }

    public function baseUrl()
    {
        return ServiceProviderConfig::BASE_URL;
    }

    public function apiVersion()
    {
        return ServiceProviderConfig::API_VERSION;
    }

    public function timeoutSeconds()
    {
        return ServiceProviderConfig::TIMEOUT_SECONDS;
    }

    public function maxRetries()
    {
        return ServiceProviderConfig::MAX_RETRIES;
    }

    public function retryDelaySeconds()
    {
        return ServiceProviderConfig::RETRY_DELAY_SECONDS;
    }

    public function authType()
    {
        return ServiceProviderConfig::AUTH_TYPE;
    }

    public function apiKey()
    {
        return ServiceProviderConfig::API_KEY;
    }

    public function apiSecret()
    {
        return ServiceProviderConfig::API_SECRET;
    }

    public function apiToken()
    {
        return ServiceProviderConfig::API_TOKEN;
    }

    public function publicKey()
    {
        return ServiceProviderConfig::PUBLIC_KEY;
    }

    public function privateKey()
    {
        return ServiceProviderConfig::PRIVATE_KEY;
    }

    public function tokenExpiry()
    {
        return ServiceProviderConfig::TOKEN_EXPIRY;
    }

    public function username()
    {
        return ServiceProviderConfig::USERNAME;
    }

    public function password()
    {
        return ServiceProviderConfig::PASSWORD;
    }

    public function defaultHeaders()
    {
        return ServiceProviderConfig::DEFAULT_HEADERS;
    }

    public function dynamicHeaders()
    {
        return ServiceProviderConfig::DYNAMIC_HEADERS;
    }

    public function rateLimitPerMinute()
    {
        return ServiceProviderConfig::RATE_LIMIT_PER_MINUTE;
    }

    public function rateLimitPerHour()
    {
        return ServiceProviderConfig::RATE_LIMIT_PER_HOUR;
    }

    public function rateLimitPerDay()
    {
        return ServiceProviderConfig::RATE_LIMIT_PER_DAY;
    }

    public function verifySsl()
    {
        return ServiceProviderConfig::VERIFY_SSL;
    }

    public function sslCertPath()
    {
        return ServiceProviderConfig::SSL_CERT_PATH;
    }

    public function sslKeyPath()
    {
        return ServiceProviderConfig::SSL_KEY_PATH;
    }

    public function healthCheckUrl()
    {
        return ServiceProviderConfig::HEALTH_CHECK_URL;
    }

    public function healthCheckInterval()
    {
        return ServiceProviderConfig::HEALTH_CHECK_INTERVAL;
    }

    public function lastHealthCheck()
    {
        return ServiceProviderConfig::LAST_HEALTH_CHECK;
    }

    public function healthStatus()
    {
        return ServiceProviderConfig::HEALTH_STATUS;
    }

    public function healthMessage()
    {
        return ServiceProviderConfig::HEALTH_MESSAGE;
    }

    public function avgResponseTime()
    {
        return ServiceProviderConfig::AVG_RESPONSE_TIME;
    }

    public function successRate()
    {
        return ServiceProviderConfig::SUCCESS_RATE;
    }

    public function totalCalls()
    {
        return ServiceProviderConfig::TOTAL_CALLS;
    }

    public function successfulCalls()
    {
        return ServiceProviderConfig::SUCCESSFUL_CALLS;
    }

    public function failedCalls()
    {
        return ServiceProviderConfig::FAILED_CALLS;
    }

    // functions
    public function getConfigByProviderCodeAndEnvironment(string $providerCode, string $environment, ServiceProviderRepository $serviceProviderRepository)
    {
        $configQuery = $this->query()
            ->join(
                'service_providers',
                'service_provider_configs.' . $this->providerId(),
                '=',
                'service_providers.' . $serviceProviderRepository->id()
            );

        $config = (clone $configQuery)
            ->where('service_providers.' . $serviceProviderRepository->providerCode(), $providerCode)
            ->where('service_provider_configs.' . $this->environment(), $environment)
            ->select('service_provider_configs.*')
            ->first();

        // Fallback to any active configuration if environment match is not found
        if (!$config) {
            $config = clone $configQuery;
            $config = $config->where('service_providers.' . $serviceProviderRepository->providerCode(), $providerCode)
                ->where('service_provider_configs.' . $this->status(), 'active')
                ->select('service_provider_configs.*')
                ->first();
        }

        return $config;
    }
}
