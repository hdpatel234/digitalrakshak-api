<?php

namespace App\Services;

use App\Repositories\CandidateServiceLogRepository;

/**
 * @property CandidateServiceLogRepository $repository
 */
class CandidateServiceLogService extends BaseService
{
    public function __construct(CandidateServiceLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function candidateId()
    {
        return $this->repository->candidateId();
    }

    public function candidateServiceId()
    {
        return $this->repository->candidateServiceId();
    }

    public function title()
    {
        return $this->repository->title();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function status()
    {
        return $this->repository->status();
    }
}
