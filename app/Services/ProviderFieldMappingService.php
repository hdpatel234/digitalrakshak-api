<?php

namespace App\Services;

use App\Repositories\ProviderFieldMappingRepository;

/**
 * @property ProviderFieldMappingRepository $repository
 */
class ProviderFieldMappingService extends BaseService
{
    
    public function __construct(ProviderFieldMappingRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function serviceProviderAssignmentId()
    {
        return $this->repository->serviceProviderAssignmentId();
    }

    public function serviceFieldId()
    {
        return $this->repository->serviceFieldId();
    }

    public function providerFieldName()
    {
        return $this->repository->providerFieldName();
    }

    public function fieldPath()
    {
        return $this->repository->fieldPath();
    }

    public function transformFunction()
    {
        return $this->repository->transformFunction();
    }

    public function isRequired()
    {
        return $this->repository->isRequired();
    }

    public function defaultValue()
    {
        return $this->repository->defaultValue();
    }
    // functions
}