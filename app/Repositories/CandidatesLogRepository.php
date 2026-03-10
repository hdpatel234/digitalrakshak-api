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
        return $this->model::CANDIDATE_ID;
    }

    public function action()
    {
        return $this->model::ACTION;
    }

    public function ipAddress()
    {
        return $this->model::IP_ADDRESS;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }

    public function deletedBy()
    {
        return $this->model::DELETED_BY;
    }
    // functions
}