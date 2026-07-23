<?php

namespace App\Services;

use App\Repositories\ServiceProviderPerformanceLogRepository;

/**
 * @property ServiceProviderPerformanceLogRepository $repository
 */
class ServiceProviderPerformanceLogService extends BaseService
{

    public function __construct(ServiceProviderPerformanceLogRepository $repository)
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

    public function assignmentId()
    {
        return $this->repository->assignmentId();
    }

    public function responseTimeMs()
    {
        return $this->repository->responseTimeMs();
    }

    public function statusCode()
    {
        return $this->repository->statusCode();
    }

    public function success()
    {
        return $this->repository->success();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }

    public function loggedAt()
    {
        return $this->repository->loggedAt();
    }

    // functions
}
