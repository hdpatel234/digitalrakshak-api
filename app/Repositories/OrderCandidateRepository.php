<?php

namespace App\Repositories;

use App\Models\OrderCandidate;

class OrderCandidateRepository extends BaseRepository
{
    public function __construct(OrderCandidate $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function orderId()
    {
        return OrderCandidate::ORDER_ID;
    }

    public function candidateId()
    {
        return OrderCandidate::CANDIDATE_ID;
    }

    public function candidateData()
    {
        return OrderCandidate::CANDIDATE_DATA;
    }

    // functions
}
