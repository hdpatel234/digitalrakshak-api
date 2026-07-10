<?php

namespace App\Services;

use App\Repositories\SupportPlatformRepository;

/**
 * @property SupportPlatformRepository $repository
 */
class SupportPlatformService extends BaseService
{
    
    public function __construct(SupportPlatformRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function platformName()
    {
        return $this->repository->platformName();
    }

    public function platformCode()
    {
        return $this->repository->platformCode();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }
    // functions
}