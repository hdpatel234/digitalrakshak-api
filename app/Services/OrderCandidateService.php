<?php

namespace App\Services;

use App\Repositories\OrderCandidateRepository;

/**
 * @property OrderCandidateRepository $repository
 */
class OrderCandidateService extends BaseService
{
    
    public function __construct(OrderCandidateRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function orderId()
    {
        return $this->repository->orderId();
    }

    public function candidateId()
    {
        return $this->repository->candidateId();
    }

    public function candidateData()
    {
        return $this->repository->candidateData();
    }

    public function subtotal()
    {
        return $this->repository->subtotal();
    }

    public function discountAmount()
    {
        return $this->repository->discountAmount();
    }

    public function taxAmount()
    {
        return $this->repository->taxAmount();
    }

    public function totalAmount()
    {
        return $this->repository->totalAmount();
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