<?php

namespace App\Services;

use App\Repositories\ServiceProviderOutageRepository;

/**
 * @property ServiceProviderOutageRepository $repository
 */
class ServiceProviderOutageService extends BaseService
{

    public function __construct(ServiceProviderOutageRepository $repository)
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
