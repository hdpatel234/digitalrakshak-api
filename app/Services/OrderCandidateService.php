<?php

namespace App\Services;

use App\Repositories\OrderCandidateRepository;

/**
 * @property OrderCandidateRepository $repository
 */
class OrderCandidateService extends BaseService
{

    public function __construct(OrderCandidateRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function orderId()
    {
        return $this->repository->orderId();
    }

    public function candidateId()
    {
        return $this->repository->candidateId();
    }

    public function candidateData()
    {
        return $this->repository->candidateData();
    }

    // functions
}
