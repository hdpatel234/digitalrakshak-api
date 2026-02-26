<?php

namespace App\Repositories;

use App\Models\ServiceProcessingQueue;

class ServiceProcessingQueueRepository extends BaseRepository
{
    public function __construct(ServiceProcessingQueue $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function orderItemId()
    {
        return $this->model::ORDER_ITEM_ID;
    }

    public function serviceId()
    {
        return $this->model::SERVICE_ID;
    }

    public function candidateId()
    {
        return $this->model::CANDIDATE_ID;
    }

    public function processingRuleId()
    {
        return $this->model::PROCESSING_RULE_ID;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function attempts()
    {
        return $this->model::ATTEMPTS;
    }

    public function maxAttempts()
    {
        return $this->model::MAX_ATTEMPTS;
    }

    public function requestData()
    {
        return $this->model::REQUEST_DATA;
    }

    public function responseData()
    {
        return $this->model::RESPONSE_DATA;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }

    public function processedAt()
    {
        return $this->model::PROCESSED_AT;
    }

    public function nextRetryAt()
    {
        return $this->model::NEXT_RETRY_AT;
    }

    public function completedAt()
    {
        return $this->model::COMPLETED_AT;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }
    // functions
}