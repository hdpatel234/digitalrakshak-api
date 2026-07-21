<?php

namespace App\Services;

use App\Repositories\ServicesFieldRepository;

/**
 * @property ServicesFieldRepository $repository
 */
class ServicesFieldService extends BaseService
{
    
    public function __construct(ServicesFieldRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function serviceId()
    {
        return $this->repository->serviceId();
    }

    public function fieldName()
    {
        return $this->repository->fieldName();
    }

    public function fieldLabel()
    {
        return $this->repository->fieldLabel();
    }

    public function fieldType()
    {
        return $this->repository->fieldType();
    }

    public function isRequired()
    {
        return $this->repository->isRequired();
    }

    public function validationRegex()
    {
        return $this->repository->validationRegex();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
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
