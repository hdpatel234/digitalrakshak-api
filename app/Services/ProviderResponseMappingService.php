<?php

namespace App\Services;

use App\Repositories\ProviderResponseMappingRepository;

class ProviderResponseMappingService extends BaseService
{
    
    public function __construct(ProviderResponseMappingRepository $repository)
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