<?php

namespace App\Services;

use App\Repositories\ProviderCostRepository;

/**
 * @property ProviderCostRepository $repository
 */
class ProviderCostService extends BaseService
{
    
    public function __construct(ProviderCostRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function providerId()
    {
        return $this->repository->providerId();
    }

    public function serviceId()
    {
        return $this->repository->serviceId();
    }

    public function costPerCall()
    {
        return $this->repository->costPerCall();
    }

    public function currency()
    {
        return $this->repository->currency();
    }

    public function billingModel()
    {
        return $this->repository->billingModel();
    }

    public function minimumCommitment()
    {
        return $this->repository->minimumCommitment();
    }

    public function commitmentPeriod()
    {
        return $this->repository->commitmentPeriod();
    }

    public function effectiveFrom()
    {
        return $this->repository->effectiveFrom();
    }

    public function effectiveTo()
    {
        return $this->repository->effectiveTo();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }
    // functions
}