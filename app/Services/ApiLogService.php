<?php

namespace App\Services;

use App\Repositories\ApiLogRepository;

class ApiLogService extends BaseService
{
    protected $repository;
    
    public function __construct(ApiLogRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function serviceId()
    {
        return $this->repository->serviceId();
    }

    public function orderItemId()
    {
        return $this->repository->orderItemId();
    }

    public function endpoint()
    {
        return $this->repository->endpoint();
    }

    public function method()
    {
        return $this->repository->method();
    }

    public function requestData()
    {
        return $this->repository->requestData();
    }

    public function responseData()
    {
        return $this->repository->responseData();
    }

    public function httpStatus()
    {
        return $this->repository->httpStatus();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }

    public function durationMs()
    {
        return $this->repository->durationMs();
    }

    public function ipAddress()
    {
        return $this->repository->ipAddress();
    }
    // functions
}