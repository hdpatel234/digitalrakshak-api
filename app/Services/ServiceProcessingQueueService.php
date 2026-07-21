<?php

namespace App\Services;

use App\Repositories\ServiceProcessingQueueRepository;

/**
 * @property ServiceProcessingQueueRepository $repository
 */
class ServiceProcessingQueueService extends BaseService
{
    
    public function __construct(ServiceProcessingQueueRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function orderItemId()
    {
        return $this->repository->orderItemId();
    }

    public function serviceId()
    {
        return $this->repository->serviceId();
    }

    public function candidateId()
    {
        return $this->repository->candidateId();
    }

    public function processingRuleId()
    {
        return $this->repository->processingRuleId();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function attempts()
    {
        return $this->repository->attempts();
    }

    public function maxAttempts()
    {
        return $this->repository->maxAttempts();
    }

    public function requestData()
    {
        return $this->repository->requestData();
    }

    public function responseData()
    {
        return $this->repository->responseData();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }

    public function processedAt()
    {
        return $this->repository->processedAt();
    }

    public function nextRetryAt()
    {
        return $this->repository->nextRetryAt();
    }

    public function completedAt()
    {
        return $this->repository->completedAt();
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
