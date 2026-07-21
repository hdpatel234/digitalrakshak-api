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
        return CandidateManager::CANDIDATE_ID;
    }

    public function email()
    {
        return CandidateManager::EMAIL;
    }

    public function status()
    {
        return CandidateManager::STATUS;
    }

    public function createdBy()
    {
        return CandidateManager::CREATED_BY;
    }

    public function updatedBy()
    {
        return CandidateManager::UPDATED_BY;
    }

    public function deletedBy()
    {
        return CandidateManager::DELETED_BY;
    }
    // functions
}
