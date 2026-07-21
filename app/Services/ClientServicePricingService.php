<?php

namespace App\Services;

use App\Repositories\ClientServicePricingRepository;

/**
 * @property ClientServicePricingRepository $repository
 */
class ClientServicePricingService extends BaseService
{
    
    public function __construct(ClientServicePricingRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function serviceId()
    {
        return $this->repository->serviceId();
    }

    public function customPrice()
    {
        return $this->repository->customPrice();
    }

    public function effectiveFrom()
    {
        return $this->repository->effectiveFrom();
    }

    public function effectiveTo()
    {
        return $this->repository->effectiveTo();
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
