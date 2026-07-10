<?php

namespace App\Services;

use App\Repositories\SupportConfigRepository;

/**
 * @property SupportConfigRepository $repository
 */
class SupportConfigService extends BaseService
{
    protected $repository;
    public function __construct(SupportConfigRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function supportPlatformId()
    {
        return $this->repository->supportPlatformId();
    }

    public function configName()
    {
        return $this->repository->configName();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function apiUrl()
    {
        return $this->repository->apiUrl();
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

    public function webhookSecret()
    {
        return $this->repository->webhookSecret();
    }

    public function additionalConfig()
    {
        return $this->repository->additionalConfig();
    }

    public function defaultPriority()
    {
        return $this->repository->defaultPriority();
    }

    public function defaultDepartment()
    {
        return $this->repository->defaultDepartment();
    }

    public function defaultAssignee()
    {
        return $this->repository->defaultAssignee();
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