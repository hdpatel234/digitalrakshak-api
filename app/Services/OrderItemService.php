<?php

namespace App\Services;

use App\Repositories\OrderItemRepository;

/**
 * @property OrderItemRepository $repository
 */
class OrderItemService extends BaseService
{
    public function __construct(OrderItemRepository $repository)
    {
        $this->repository = $repository;
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

    public function unitPrice()
    {
        return $this->repository->unitPrice();
    }

    public function quantity()
    {
        return $this->repository->quantity();
    }

    public function discountAmount()
    {
        return $this->repository->discountAmount();
    }

    public function taxAmount()
    {
        return $this->repository->taxAmount();
    }

    public function taxPercentage()
    {
        return $this->repository->taxPercentage();
    }

    public function totalPrice()
    {
        return $this->repository->totalPrice();
    }

    public function serviceData()
    {
        return $this->repository->serviceData();
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