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
        return $this->model::ORDER_ID;
    }

    public function candidateId()
    {
        return $this->model::CANDIDATE_ID;
    }

    public function candidateData()
    {
        return $this->model::CANDIDATE_DATA;
    }

    public function subtotal()
    {
        return $this->model::SUBTOTAL;
    }

    public function discountAmount()
    {
        return $this->model::DISCOUNT_AMOUNT;
    }

    public function taxAmount()
    {
        return $this->model::TAX_AMOUNT;
    }

    public function totalAmount()
    {
        return $this->model::TOTAL_AMOUNT;
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