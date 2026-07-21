<?php

namespace App\Services;

use App\Repositories\ServiceDependencyRepository;

/**
 * @property ServiceDependencyRepository $repository
 */
class ServiceDependencyService extends BaseService
{
    
    public function __construct(ServiceDependencyRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function serviceId()
    {
        return $this->repository->serviceId();
    }

    public function dependsOnServiceId()
    {
        return $this->repository->dependsOnServiceId();
    }

    public function dependencyType()
    {
        return $this->repository->dependencyType();
    }
    // functions
}
