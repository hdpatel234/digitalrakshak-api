<?php

namespace App\Services;

use App\Repositories\AiUsageLogRepository;

/**
 * @property AiUsageLogRepository $repository
 */
class AiUsageLogService extends BaseService
{
    protected $repository;
    
    public function __construct(AiUsageLogRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function configId()
    {
        return $this->repository->configId();
    }

    public function modelId()
    {
        return $this->repository->modelId();
    }

    public function userId()
    {
        return $this->repository->userId();
    }

    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function conversationId()
    {
        return $this->repository->conversationId();
    }

    public function messageId()
    {
        return $this->repository->messageId();
    }

    public function inputTokens()
    {
        return $this->repository->inputTokens();
    }

    public function outputTokens()
    {
        return $this->repository->outputTokens();
    }

    public function totalTokens()
    {
        return $this->repository->totalTokens();
    }

    public function cost()
    {
        return $this->repository->cost();
    }

    public function requestType()
    {
        return $this->repository->requestType();
    }

    public function responseTimeMs()
    {
        return $this->repository->responseTimeMs();
    }

    public function success()
    {
        return $this->repository->success();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }
    // functions
}