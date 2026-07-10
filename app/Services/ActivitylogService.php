<?php

namespace App\Services;

use App\Repositories\ActivitylogRepository;

/**
 * @property ActivitylogRepository $repository
 */
class ActivitylogService extends BaseService
{
    
    public function __construct(ActivitylogRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function description()
    {
        return $this->repository->description();
    }

    public function userId()
    {
        return $this->repository->userId();
    }

    public function date()
    {
        return $this->repository->date();
    }

    public function ipAddress()
    {
        return $this->repository->ipAddress();
    }
    // functions
}