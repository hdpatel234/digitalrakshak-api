<?php

namespace App\Services;

use App\Repositories\CandidateServiceRepository;

class CandidateServiceService extends BaseService
{
    protected $repository;
    
    public function __construct(CandidateServiceRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function candidateId()
    {
        return $this->repository->candidateId();
    }

    public function serviceId()
    {
        return $this->repository->serviceId();
    }

    public function orderId()
    {
        return $this->repository->orderId();
    }

    public function orderItemId()
    {
        return $this->repository->orderItemId();
    }

    public function pricePaid()
    {
        return $this->repository->pricePaid();
    }

    public function processingRuleId()
    {
        return $this->repository->processingRuleId();
    }

    public function processingStatus()
    {
        return $this->repository->processingStatus();
    }

    public function processingAttempts()
    {
        return $this->repository->processingAttempts();
    }

    public function processedAt()
    {
        return $this->repository->processedAt();
    }

    public function completedAt()
    {
        return $this->repository->completedAt();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }

    public function deletedBy()
    {
        return $this->repository->deletedBy();
    }
    // functions
}