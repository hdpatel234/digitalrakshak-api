<?php

namespace App\Services;

use App\Repositories\ServiceProviderAssignmentRepository;

/**
 * @property ServiceProviderAssignmentRepository $repository
 */
class ServiceProviderAssignmentService extends BaseService
{
    
    public function __construct(ServiceProviderAssignmentRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function serviceId()
    {
        return $this->repository->serviceId();
    }

    public function providerId()
    {
        return $this->repository->providerId();
    }

    public function providerConfigId()
    {
        return $this->repository->providerConfigId();
    }

    public function priority()
    {
        return $this->repository->priority();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function isPrimary()
    {
        return $this->repository->isPrimary();
    }

    public function isBackup()
    {
        return $this->repository->isBackup();
    }

    public function fallbackThreshold()
    {
        return $this->repository->fallbackThreshold();
    }

    public function cooldownPeriod()
    {
        return $this->repository->cooldownPeriod();
    }

    public function endpointOverride()
    {
        return $this->repository->endpointOverride();
    }

    public function methodOverride()
    {
        return $this->repository->methodOverride();
    }

    public function headersOverride()
    {
        return $this->repository->headersOverride();
    }

    public function bodyTemplate()
    {
        return $this->repository->bodyTemplate();
    }

    public function currentStatus()
    {
        return $this->repository->currentStatus();
    }

    public function failureCount()
    {
        return $this->repository->failureCount();
    }

    public function lastFailureAt()
    {
        return $this->repository->lastFailureAt();
    }

    public function lastSuccessAt()
    {
        return $this->repository->lastSuccessAt();
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