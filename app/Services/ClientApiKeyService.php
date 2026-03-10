<?php

namespace App\Services;

use App\Repositories\ClientApiKeyRepository;

class ClientApiKeyService extends BaseService
{
    
    public function __construct(ClientApiKeyRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function keyName()
    {
        return $this->repository->keyName();
    }

    public function apiKey()
    {
        return $this->repository->apiKey();
    }

    public function apiSecret()
    {
        return $this->repository->apiSecret();
    }

    public function keyType()
    {
        return $this->repository->keyType();
    }

    public function permissions()
    {
        return $this->repository->permissions();
    }

    public function ipWhitelist()
    {
        return $this->repository->ipWhitelist();
    }

    public function rateLimit()
    {
        return $this->repository->rateLimit();
    }

    public function rateLimitPerDay()
    {
        return $this->repository->rateLimitPerDay();
    }

    public function expiresAt()
    {
        return $this->repository->expiresAt();
    }

    public function lastUsedAt()
    {
        return $this->repository->lastUsedAt();
    }

    public function lastUsedIp()
    {
        return $this->repository->lastUsedIp();
    }

    public function totalRequests()
    {
        return $this->repository->totalRequests();
    }

    public function status()
    {
        return $this->repository->status();
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