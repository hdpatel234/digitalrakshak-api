<?php

namespace App\Services;

use App\Repositories\PackageServiceRepository;

class PackageServiceService extends BaseService
{
    
    public function __construct(PackageServiceRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function packageId()
    {
        return $this->repository->packageId();
    }

    public function serviceId()
    {
        return $this->repository->serviceId();
    }

    public function priceOverride()
    {
        return $this->repository->priceOverride();
    }

    public function isMandatory()
    {
        return $this->repository->isMandatory();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
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