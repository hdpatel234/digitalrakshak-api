<?php

namespace App\Services;

use App\Repositories\PaymentMethodTypeRepository;

/**
 * @property PaymentMethodTypeRepository $repository
 */
class PaymentMethodTypeService extends BaseService
{
    public function __construct(PaymentMethodTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function methodName()
    {
        return $this->repository->methodName();
    }

    public function methodCode()
    {
        return $this->repository->methodCode();
    }

    public function category()
    {
        return $this->repository->category();
    }

    public function icon()
    {
        return $this->repository->icon();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function configurationSchema()
    {
        return $this->repository->configurationSchema();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
    }
    // functions
}
