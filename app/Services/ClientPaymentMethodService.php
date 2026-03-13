<?php

namespace App\Services;

use App\Repositories\ClientPaymentMethodRepository;

class ClientPaymentMethodService extends BaseService
{
    protected $repository;
    
    public function __construct(ClientPaymentMethodRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function methodTypeId()
    {
        return $this->repository->methodTypeId();
    }

    public function gatewayConfigId()
    {
        return $this->repository->gatewayConfigId();
    }

    public function displayName()
    {
        return $this->repository->displayName();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function icon()
    {
        return $this->repository->icon();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
    }

    public function isEnabled()
    {
        return $this->repository->isEnabled();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function minAmount()
    {
        return $this->repository->minAmount();
    }

    public function maxAmount()
    {
        return $this->repository->maxAmount();
    }

    public function instructions()
    {
        return $this->repository->instructions();
    }
    // functions
}