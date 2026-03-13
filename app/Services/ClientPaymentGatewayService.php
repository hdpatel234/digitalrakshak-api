<?php

namespace App\Services;

use App\Repositories\ClientPaymentGatewayRepository;

class ClientPaymentGatewayService extends BaseService
{
    protected $repository;
    
    public function __construct(ClientPaymentGatewayRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function gatewayConfigId()
    {
        return $this->repository->gatewayConfigId();
    }

    public function displayName()
    {
        return $this->repository->displayName();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
    }

    public function enabledMethods()
    {
        return $this->repository->enabledMethods();
    }

    public function currencies()
    {
        return $this->repository->currencies();
    }

    public function feeType()
    {
        return $this->repository->feeType();
    }

    public function feeFixed()
    {
        return $this->repository->feeFixed();
    }

    public function feePercentage()
    {
        return $this->repository->feePercentage();
    }

    public function minAmount()
    {
        return $this->repository->minAmount();
    }

    public function maxAmount()
    {
        return $this->repository->maxAmount();
    }

    public function dailyLimit()
    {
        return $this->repository->dailyLimit();
    }

    public function monthlyLimit()
    {
        return $this->repository->monthlyLimit();
    }

    public function isEnabled()
    {
        return $this->repository->isEnabled();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function isMandatory()
    {
        return $this->repository->isMandatory();
    }

    public function totalTransactions()
    {
        return $this->repository->totalTransactions();
    }

    public function totalAmount()
    {
        return $this->repository->totalAmount();
    }

    public function lastTransactionAt()
    {
        return $this->repository->lastTransactionAt();
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