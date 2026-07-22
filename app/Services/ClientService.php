<?php

namespace App\Services;

use App\Repositories\ClientRepository;

/**
 * @property ClientRepository $repository
 */
class ClientService extends BaseService
{

    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function companyName()
    {
        return $this->repository->companyName();
    }

    public function email()
    {
        return $this->repository->email();
    }

    public function phone()
    {
        return $this->repository->phone();
    }

    public function gstNumber()
    {
        return $this->repository->gstNumber();
    }

    public function panNumber()
    {
        return $this->repository->panNumber();
    }

    public function address()
    {
        return $this->repository->address();
    }

    public function countryID()
    {
        return $this->repository->countryID();
    }

    public function stateID()
    {
        return $this->repository->stateID();
    }

    public function cityId()
    {
        return $this->repository->cityID();
    }

    public function pincode()
    {
        return $this->repository->pincode();
    }

    // functions
}
