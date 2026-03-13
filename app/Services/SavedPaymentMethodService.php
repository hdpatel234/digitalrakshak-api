<?php

namespace App\Services;

use App\Repositories\SavedPaymentMethodRepository;

class SavedPaymentMethodService extends BaseService
{
    protected $repository;
    
    public function __construct(SavedPaymentMethodRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function userId()
    {
        return $this->repository->userId();
    }

    public function customerId()
    {
        return $this->repository->customerId();
    }

    public function gatewayConfigId()
    {
        return $this->repository->gatewayConfigId();
    }

    public function methodTypeId()
    {
        return $this->repository->methodTypeId();
    }

    public function gatewayCustomerId()
    {
        return $this->repository->gatewayCustomerId();
    }

    public function gatewayPaymentMethodId()
    {
        return $this->repository->gatewayPaymentMethodId();
    }

    public function paymentToken()
    {
        return $this->repository->paymentToken();
    }

    public function displayName()
    {
        return $this->repository->displayName();
    }

    public function maskedDetails()
    {
        return $this->repository->maskedDetails();
    }

    public function expiryMonth()
    {
        return $this->repository->expiryMonth();
    }

    public function expiryYear()
    {
        return $this->repository->expiryYear();
    }

    public function cardHolderName()
    {
        return $this->repository->cardHolderName();
    }

    public function cardBrand()
    {
        return $this->repository->cardBrand();
    }

    public function bankName()
    {
        return $this->repository->bankName();
    }

    public function upiId()
    {
        return $this->repository->upiId();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function lastUsedAt()
    {
        return $this->repository->lastUsedAt();
    }

    public function usedCount()
    {
        return $this->repository->usedCount();
    }
    // functions
}