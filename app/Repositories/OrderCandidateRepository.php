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

    public function subtotal()
    {
        return OrderCandidate::SUBTOTAL;
    }

    public function discountAmount()
    {
        return OrderCandidate::DISCOUNT_AMOUNT;
    }

    public function taxAmount()
    {
        return OrderCandidate::TAX_AMOUNT;
    }

    public function totalAmount()
    {
        return OrderCandidate::TOTAL_AMOUNT;
    }

    public function status()
    {
        return OrderCandidate::STATUS;
    }

    public function createdBy()
    {
        return OrderCandidate::CREATED_BY;
    }

    public function updatedBy()
    {
        return OrderCandidate::UPDATED_BY;
    }

    public function deletedBy()
    {
        return OrderCandidate::DELETED_BY;
    }
    // functions
}