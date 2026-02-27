<?php

namespace App\Services;

use App\Repositories\ClientApiLogRepository;

class ClientApiLogService extends BaseService
{
    protected $repository;
    
    public function __construct(ClientApiLogRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function apiKeyId()
    {
        return $this->repository->apiKeyId();
    }

    public function endpoint()
    {
        return $this->repository->endpoint();
    }

    public function method()
    {
        return $this->repository->method();
    }

    public function requestHeaders()
    {
        return $this->repository->requestHeaders();
    }

    public function requestBody()
    {
        return $this->repository->requestBody();
    }

    public function responseCode()
    {
        return $this->repository->responseCode();
    }

    public function responseBody()
    {
        return $this->repository->responseBody();
    }

    public function responseTimeMs()
    {
        return $this->repository->responseTimeMs();
    }

    public function ipAddress()
    {
        return $this->repository->ipAddress();
    }

    public function userAgent()
    {
        return $this->repository->userAgent();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }
    // functions
}