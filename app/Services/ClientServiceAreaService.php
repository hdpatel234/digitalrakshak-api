<?php

namespace App\Services;

use App\Repositories\ClientServiceAreaRepository;

class ClientServiceAreaService extends BaseService
{
    
    public function __construct(ClientServiceAreaRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
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

    public function serviceType()
    {
        return $this->repository->serviceType();
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