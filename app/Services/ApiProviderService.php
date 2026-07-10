<?php

namespace App\Services;

use App\Repositories\ApiProviderRepository;

/**
 * @property ApiProviderRepository $repository
 */
class ApiProviderService extends BaseService
{
    
    public function __construct(ApiProviderRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function name()
    {
        return $this->repository->name();
    }

    public function code()
    {
        return $this->repository->code();
    }

    public function class()
    {
        return $this->repository->class();
    }

    public function configFields()
    {
        return $this->repository->configFields();
    }

    public function credentials()
    {
        return $this->repository->credentials();
    }

    public function settings()
    {
        return $this->repository->settings();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function priority()
    {
        return $this->repository->priority();
    }
    // functions
}