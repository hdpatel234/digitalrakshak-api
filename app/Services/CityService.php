<?php

namespace App\Services;

use App\Repositories\CityRepository;

class CityService extends BaseService
{
    protected $repository;
    
    public function __construct(CityRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function stateId()
    {
        return $this->repository->stateId();
    }

    public function countryId()
    {
        return $this->repository->countryId();
    }

    public function name()
    {
        return $this->repository->name();
    }

    public function localName()
    {
        return $this->repository->localName();
    }

    public function district()
    {
        return $this->repository->district();
    }

    public function latitude()
    {
        return $this->repository->latitude();
    }

    public function longitude()
    {
        return $this->repository->longitude();
    }

    public function postalCode()
    {
        return $this->repository->postalCode();
    }

    public function postalCodes()
    {
        return $this->repository->postalCodes();
    }

    public function timezone()
    {
        return $this->repository->timezone();
    }

    public function isCapital()
    {
        return $this->repository->isCapital();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
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