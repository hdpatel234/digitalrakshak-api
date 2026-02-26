<?php

namespace App\Services;

use App\Repositories\CandidateServiceDatumRepository;

class CandidateServiceDatumService extends BaseService
{
    protected $repository;
    
    public function __construct(CandidateServiceDatumRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function candidateServiceId()
    {
        return $this->repository->candidateServiceId();
    }

    public function fieldId()
    {
        return $this->repository->fieldId();
    }

    public function fieldValue()
    {
        return $this->repository->fieldValue();
    }

    public function isVerified()
    {
        return $this->repository->isVerified();
    }

    public function verifiedAt()
    {
        return $this->repository->verifiedAt();
    }

    public function verifiedBy()
    {
        return $this->repository->verifiedBy();
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