<?php

namespace App\Services;

use App\Repositories\ProviderApiConfigRepository;

/**
 * @property ProviderApiConfigRepository $repository
 */
class ProviderApiConfigService extends BaseService
{

    public function __construct(ProviderApiConfigRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function providerId()
    {
        return $this->repository->providerId();
    }

    public function configName()
    {
        return $this->repository->configName();
    }

    public function environment()
    {
        return $this->repository->environment();
    }

    public function baseUrl()
    {
        return $this->repository->baseUrl();
    }

    public function apiVersion()
    {
        return $this->repository->apiVersion();
    }

    public function timeoutSeconds()
    {
        return $this->repository->timeoutSeconds();
    }

    public function maxRetries()
    {
        return $this->repository->maxRetries();
    }

    public function retryDelaySeconds()
    {
        return $this->repository->retryDelaySeconds();
    }

    public function authType()
    {
        return $this->repository->authType();
    }

    public function apiKey()
    {
        return $this->repository->apiKey();
    }

    public function apiSecret()
    {
        return $this->repository->apiSecret();
    }

    public function apiToken()
    {
        return $this->repository->apiToken();
    }

    public function publicKey()
    {
        return $this->repository->publicKey();
    }

    public function privateKey()
    {
        return $this->repository->privateKey();
    }

    public function tokenExpiry()
    {
        return $this->repository->tokenExpiry();
    }

    public function username()
    {
        return $this->repository->username();
    }

    public function password()
    {
        return $this->repository->password();
    }

    public function defaultHeaders()
    {
        return $this->repository->defaultHeaders();
    }

    public function dynamicHeaders()
    {
        return $this->repository->dynamicHeaders();
    }

    public function rateLimitPerMinute()
    {
        return $this->repository->rateLimitPerMinute();
    }

    public function rateLimitPerHour()
    {
        return $this->repository->rateLimitPerHour();
    }

    public function rateLimitPerDay()
    {
        return $this->repository->rateLimitPerDay();
    }

    public function verifySsl()
    {
        return $this->repository->verifySsl();
    }

    public function sslCertPath()
    {
        return $this->repository->sslCertPath();
    }

    public function sslKeyPath()
    {
        return $this->repository->sslKeyPath();
    }

    public function healthCheckUrl()
    {
        return $this->repository->healthCheckUrl();
    }

    public function healthCheckInterval()
    {
        return $this->repository->healthCheckInterval();
    }

    public function lastHealthCheck()
    {
        return $this->repository->lastHealthCheck();
    }

    public function healthStatus()
    {
        return $this->repository->healthStatus();
    }

    public function healthMessage()
    {
        return $this->repository->healthMessage();
    }

    public function avgResponseTime()
    {
        return $this->repository->avgResponseTime();
    }

    public function successRate()
    {
        return $this->repository->successRate();
    }

    public function totalCalls()
    {
        return $this->repository->totalCalls();
    }

    public function successfulCalls()
    {
        return $this->repository->successfulCalls();
    }

    public function failedCalls()
    {
        return $this->repository->failedCalls();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }
    // functions
}
