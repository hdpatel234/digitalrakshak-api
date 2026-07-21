<?php

namespace App\Services;

use App\Repositories\CandidateServiceDataRepository;

/**
 * @property CandidateServiceDataRepository $repository
 */
class CandidateServiceDataService extends BaseService
{

    public function __construct(CandidateServiceDataRepository $repository)
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
    // functions
}
