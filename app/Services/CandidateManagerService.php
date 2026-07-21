<?php

namespace App\Services;

use App\Repositories\CandidateManagerRepository;

/**
 * @property CandidateManagerRepository $repository
 */
class CandidateManagerService extends BaseService
{
    
    public function __construct(CandidateManagerRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function candidateId()
    {
        return $this->repository->candidateId();
    }

    public function email()
    {
        return $this->repository->email();
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
