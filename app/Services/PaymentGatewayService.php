<?php

namespace App\Services;

use App\Repositories\PaymentGatewayRepository;

/**
 * @property PaymentGatewayRepository $repository
 */
class PaymentGatewayService extends BaseService
{
    public function __construct(PaymentGatewayRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function gatewayName()
    {
        return $this->repository->gatewayName();
    }

    public function gatewayCode()
    {
        return $this->repository->gatewayCode();
    }

    public function providerCompany()
    {
        return $this->repository->providerCompany();
    }

    public function website()
    {
        return $this->repository->website();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function logo()
    {
        return $this->repository->logo();
    }

    public function supportedMethods()
    {
        return $this->repository->supportedMethods();
    }

    public function configurationSchema()
    {
        return $this->repository->configurationSchema();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
    }
    // functions
}
