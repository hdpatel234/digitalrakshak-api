<?php

namespace App\Repositories;

use App\Models\CandidateService;

class CandidateServiceRepository extends BaseRepository
{
    public function __construct(CandidateService $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function candidateId()
    {
        return $this->model::CANDIDATE_ID;
    }

    public function serviceId()
    {
        return $this->model::SERVICE_ID;
    }

    public function orderId()
    {
        return $this->model::ORDER_ID;
    }

    public function orderItemId()
    {
        return $this->model::ORDER_ITEM_ID;
    }

    public function pricePaid()
    {
        return $this->model::PRICE_PAID;
    }

    public function processingRuleId()
    {
        return $this->model::PROCESSING_RULE_ID;
    }

    public function processingStatus()
    {
        return $this->model::PROCESSING_STATUS;
    }

    public function processingAttempts()
    {
        return $this->model::PROCESSING_ATTEMPTS;
    }

    public function processedAt()
    {
        return $this->model::PROCESSED_AT;
    }

    public function completedAt()
    {
        return $this->model::COMPLETED_AT;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
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