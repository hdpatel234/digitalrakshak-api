<?php

namespace App\Services;

use App\Repositories\SupportPriorityRepository;

class SupportPriorityService extends BaseService
{
    protected $repository;
    
    public function __construct(SupportPriorityRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function name()
    {
        return $this->repository->name();
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