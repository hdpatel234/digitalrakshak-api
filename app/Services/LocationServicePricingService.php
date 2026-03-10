<?php

namespace App\Services;

use App\Repositories\LocationServicePricingRepository;

class LocationServicePricingService extends BaseService
{
    
    public function __construct(LocationServicePricingRepository $repository)
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

    public function countryId()
    {
        return $this->repository->countryId();
    }

    public function stateId()
    {
        return $this->repository->stateId();
    }

    public function cityId()
    {
        return $this->repository->cityId();
    }

    public function priceAdjustmentType()
    {
        return $this->repository->priceAdjustmentType();
    }

    public function priceAdjustment()
    {
        return $this->repository->priceAdjustment();
    }

    public function finalPrice()
    {
        return $this->repository->finalPrice();
    }

    public function effectiveFrom()
    {
        return $this->repository->effectiveFrom();
    }

    public function effectiveTo()
    {
        return $this->repository->effectiveTo();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }
    // functions
}