<?php

namespace App\Services;

use App\Repositories\CandidatesLogRepository;

class CandidatesLogService extends BaseService
{
    
    public function __construct(CandidatesLogRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function candidateId()
    {
        return $this->repository->candidateId();
    }

    public function action()
    {
        return $this->repository->action();
    }

    public function ipAddress()
    {
        return $this->repository->ipAddress();
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