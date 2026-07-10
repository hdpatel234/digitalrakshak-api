<?php

namespace App\Services;

use App\Repositories\ApiProviderLogRepository;

/**
 * @property ApiProviderLogRepository $repository
 */
class ApiProviderLogService extends BaseService
{
    
    public function __construct(ApiProviderLogRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function apiProviderId()
    {
        return $this->repository->apiProviderId();
    }

    public function endpoint()
    {
        return $this->repository->endpoint();
    }

    public function method()
    {
        return $this->repository->method();
    }

    public function request()
    {
        return $this->repository->request();
    }

    public function response()
    {
        return $this->repository->response();
    }

    public function responseCode()
    {
        return $this->repository->responseCode();
    }

    public function duration()
    {
        return $this->repository->duration();
    }

    public function isSuccessful()
    {
        return $this->repository->isSuccessful();
    }
    // functions
}