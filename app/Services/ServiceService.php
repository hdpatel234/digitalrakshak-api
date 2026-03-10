<?php

namespace App\Services;

use App\Repositories\ServiceRepository;

class ServiceService extends BaseService
{
    
    public function __construct(ServiceRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function serviceName()
    {
        return $this->repository->serviceName();
    }

    public function serviceCode()
    {
        return $this->repository->serviceCode();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function basePrice()
    {
        return $this->repository->basePrice();
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