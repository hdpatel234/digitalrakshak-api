<?php

namespace App\Services;

use App\Repositories\EmailServerRepository;

/**
 * @property EmailServerRepository $repository
 */
class EmailServerService extends BaseService
{
    
    public function __construct(EmailServerRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function serverName()
    {
        return $this->repository->serverName();
    }

    public function serverTypeId()
    {
        return $this->repository->serverTypeId();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function priority()
    {
        return $this->repository->priority();
    }

    public function host()
    {
        return $this->repository->host();
    }

    public function port()
    {
        return $this->repository->port();
    }

    public function encryption()
    {
        return $this->repository->encryption();
    }

    public function username()
    {
        return $this->repository->username();
    }

    public function password()
    {
        return $this->repository->password();
    }

    public function timeout()
    {
        return $this->repository->timeout();
    }

    public function verifySsl()
    {
        return $this->repository->verifySsl();
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

    public function apiEndpoint()
    {
        return $this->repository->apiEndpoint();
    }

    public function domain()
    {
        return $this->repository->domain();
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

    public function defaultFromEmail()
    {
        return $this->repository->defaultFromEmail();
    }

    public function defaultFromName()
    {
        return $this->repository->defaultFromName();
    }

    public function defaultReplyTo()
    {
        return $this->repository->defaultReplyTo();
    }

    public function serverGroup()
    {
        return $this->repository->serverGroup();
    }

    public function weight()
    {
        return $this->repository->weight();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function healthCheckAt()
    {
        return $this->repository->healthCheckAt();
    }

    public function healthCheckStatus()
    {
        return $this->repository->healthCheckStatus();
    }

    public function lastError()
    {
        return $this->repository->lastError();
    }

    public function successCount()
    {
        return $this->repository->successCount();
    }

    public function failureCount()
    {
        return $this->repository->failureCount();
    }

    public function lastUsedAt()
    {
        return $this->repository->lastUsedAt();
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