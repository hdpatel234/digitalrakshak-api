<?php

namespace App\Services;

use App\Repositories\ServiceProviderResponseMappingRepository;

/**
 * @property ServiceProviderResponseMappingRepository $repository
 */
class ServiceProviderResponseMappingService extends BaseService
{

    public function __construct(ServiceProviderResponseMappingRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function serviceProviderAssignmentId()
    {
        return $this->repository->serviceProviderAssignmentId();
    }

    public function responseField()
    {
        return $this->repository->responseField();
    }

    public function targetField()
    {
        return $this->repository->targetField();
    }

    public function dataType()
    {
        return $this->repository->dataType();
    }

    public function path()
    {
        return $this->repository->path();
    }

    public function transformFunction()
    {
        return $this->repository->transformFunction();
    }

    public function isVerificationResult()
    {
        return $this->repository->isVerificationResult();
    }

    public function isRequired()
    {
        return $this->repository->isRequired();
    }

    // functions
}
