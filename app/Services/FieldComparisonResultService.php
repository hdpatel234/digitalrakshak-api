<?php

namespace App\Services;

use App\Repositories\FieldComparisonResultRepository;

/**
 * @property FieldComparisonResultRepository $repository
 */
class FieldComparisonResultService extends BaseService
{
    public function __construct(protected FieldComparisonResultRepository $repository) {}

    // column constants
    public function verificationResultId()
    {
        return $this->repository->verificationResultId();
    }

    public function userValue()
    {
        return $this->repository->userValue();
    }

    public function apiValue()
    {
        return $this->repository->apiValue();
    }

    public function comparisonResult()
    {
        return $this->repository->comparisonResult();
    }

    public function confidenceScore()
    {
        return $this->repository->confidenceScore();
    }

    public function discrepancyNotes()
    {
        return $this->repository->discrepancyNotes();
    }
    // functions
}
