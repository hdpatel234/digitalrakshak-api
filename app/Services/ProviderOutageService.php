<?php

namespace App\Services;

use App\Repositories\ProviderOutageRepository;

/**
 * @property ProviderOutageRepository $repository
 */
class ProviderOutageService extends BaseService
{
    
    public function __construct(ProviderOutageRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function providerId()
    {
        return $this->repository->providerId();
    }

    public function serviceId()
    {
        return $this->repository->serviceId();
    }

    public function outageType()
    {
        return $this->repository->outageType();
    }

    public function startedAt()
    {
        return $this->repository->startedAt();
    }

    public function endedAt()
    {
        return $this->repository->endedAt();
    }

    public function durationMinutes()
    {
        return $this->repository->durationMinutes();
    }

    public function affectedServices()
    {
        return $this->repository->affectedServices();
    }

    public function rootCause()
    {
        return $this->repository->rootCause();
    }

    public function resolution()
    {
        return $this->repository->resolution();
    }
    // functions
}
