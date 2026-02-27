<?php

namespace App\Services;

use App\Repositories\ClientRepository;

class ClientService extends BaseService
{
    protected $repository;
    
    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function companyName()
    {
        return $this->repository->companyName();
    }

    public function contactPerson()
    {
        return $this->repository->contactPerson();
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

    public function city()
    {
        return $this->repository->city();
    }

    public function state()
    {
        return $this->repository->state();
    }

    public function pincode()
    {
        return $this->repository->pincode();
    }

    public function country()
    {
        return $this->repository->country();
    }

    public function currency()
    {
        return $this->repository->currency();
    }

    public function creditLimit()
    {
        return $this->repository->creditLimit();
    }

    public function creditBalance()
    {
        return $this->repository->creditBalance();
    }

    public function paymentTerms()
    {
        return $this->repository->paymentTerms();
    }

    public function defaultBillingConfigId()
    {
        return $this->repository->defaultBillingConfigId();
    }

    public function defaultSupportConfigId()
    {
        return $this->repository->defaultSupportConfigId();
    }

    public function defualtDocumentConfigId()
    {
        return $this->repository->defaultDocumentConfigId();
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