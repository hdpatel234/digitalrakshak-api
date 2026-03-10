<?php

namespace App\Repositories;

use App\Models\CandidateManager;

class CandidateManagerRepository extends BaseRepository
{
    public function __construct(CandidateManager $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function candidateId()
    {
        return $this->model::CANDIDATE_ID;
    }

    public function email()
    {
        return $this->model::EMAIL;
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