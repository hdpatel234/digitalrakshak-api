<?php

namespace App\Services;

use App\Repositories\StateRepository;

/**
 * @property StateRepository $repository
 */
class StateService extends BaseService
{

    public function __construct(StateRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function countryId()
    {
        return $this->repository->countryId();
    }

    public function name()
    {
        return $this->repository->name();
    }

    public function code()
    {
        return $this->repository->code();
    }

    public function type()
    {
        return $this->repository->type();
    }

    public function capital()
    {
        return $this->repository->capital();
    }

    public function latitude()
    {
        return $this->repository->latitude();
    }

    public function longitude()
    {
        return $this->repository->longitude();
    }

    public function areaKm2()
    {
        return $this->repository->areaKm2();
    }

    public function population()
    {
        return $this->repository->population();
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
    public function getByCountry($country)
    {
        return $this->repository->getByCountry($country);
    }
}
