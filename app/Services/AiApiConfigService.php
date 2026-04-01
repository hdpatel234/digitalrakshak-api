<?php

namespace App\Services;

use App\Repositories\AiApiConfigRepository;

class AiApiConfigService extends BaseService
{
    protected $repository;
    
    public function __construct(AiApiConfigRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function configName()
    {
        return $this->repository->configName();
    }

    public function providerId()
    {
        return $this->repository->providerId();
    }

    public function modelId()
    {
        return $this->repository->modelId();
    }

    public function apiKey()
    {
        return $this->repository->apiKey();
    }

    public function apiSecret()
    {
        return $this->repository->apiSecret();
    }

    public function organizationId()
    {
        return $this->repository->organizationId();
    }

    public function projectId()
    {
        return $this->repository->projectId();
    }

    public function baseUrl()
    {
        return $this->repository->baseUrl();
    }

    public function defaultModel()
    {
        return $this->repository->defaultModel();
    }

    public function defaultTemperature()
    {
        return $this->repository->defaultTemperature();
    }

    public function defaultMaxTokens()
    {
        return $this->repository->defaultMaxTokens();
    }

    public function defaultTopP()
    {
        return $this->repository->defaultTopP();
    }

    public function defaultFrequencyPenalty()
    {
        return $this->repository->defaultFrequencyPenalty();
    }

    public function defaultPresencePenalty()
    {
        return $this->repository->defaultPresencePenalty();
    }

    public function requestsPerMinute()
    {
        return $this->repository->requestsPerMinute();
    }

    public function tokensPerMinute()
    {
        return $this->repository->tokensPerMinute();
    }

    public function enableStreaming()
    {
        return $this->repository->enableStreaming();
    }

    public function enableFunctions()
    {
        return $this->repository->enableFunctions();
    }

    public function enableVision()
    {
        return $this->repository->enableVision();
    }

    public function environment()
    {
        return $this->repository->environment();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function totalRequests()
    {
        return $this->repository->totalRequests();
    }

    public function totalTokens()
    {
        return $this->repository->totalTokens();
    }

    public function totalCost()
    {
        return $this->repository->totalCost();
    }

    public function lastUsedAt()
    {
        return $this->repository->lastUsedAt();
    }

    public function healthStatus()
    {
        return $this->repository->healthStatus();
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