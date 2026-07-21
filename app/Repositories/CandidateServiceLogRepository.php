<?php

namespace App\Repositories;

use App\Models\CandidateServiceLog;

class CandidateServiceLogRepository extends BaseRepository
{
    public function __construct(CandidateServiceLog $model)
    {
        parent::__construct($model);
    }

    public function candidateId()
    {
        return CandidateServiceLog::CANDIDATE_ID;
    }

    public function candidateServiceId()
    {
        return CandidateServiceLog::order_item_id;
    }

    public function title()
    {
        return CandidateServiceLog::TITLE;
    }

    public function description()
    {
        return CandidateServiceLog::DESCRIPTION;
    }

    public function status()
    {
        return CandidateServiceLog::STATUS;
    }
}
