<?php

namespace App\Services;

use App\Repositories\PackageRepository;

class PackageService extends BaseService
{
    
    public function __construct(PackageRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function packageName()
    {
        return $this->repository->packageName();
    }

    public function packageCode()
    {
        return $this->repository->packageCode();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function type()
    {
        return $this->repository->type();
    }

    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function totalPrice()
    {
        return $this->repository->totalPrice();
    }

    public function discountType()
    {
        return $this->repository->discountType();
    }

    public function discountValue()
    {
        return $this->repository->discountValue();
    }

    public function finalPrice()
    {
        return $this->repository->finalPrice();
    }

    public function isActive()
    {
        return $this->repository->isActive();
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