<?php

namespace App\Repositories;

use App\Models\CandidatesLog;

class CandidatesLogRepository extends BaseRepository
{
    public function __construct(CandidatesLog $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function candidateId()
    {
        return CandidatesLog::CANDIDATE_ID;
    }

    public function action()
    {
        return CandidatesLog::ACTION;
    }

    public function ipAddress()
    {
        return CandidatesLog::IP_ADDRESS;
    }

    public function status()
    {
        return CandidatesLog::STATUS;
    }

    public function createdBy()
    {
        return CandidatesLog::CREATED_BY;
    }

    public function updatedBy()
    {
        return CandidatesLog::UPDATED_BY;
    }

    public function deletedBy()
    {
        return CandidatesLog::DELETED_BY;
    }
    // functions
}