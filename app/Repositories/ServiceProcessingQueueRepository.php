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
        return ServiceProcessingQueue::ORDER_ITEM_ID;
    }

    public function serviceId()
    {
        return ServiceProcessingQueue::SERVICE_ID;
    }

    public function candidateId()
    {
        return ServiceProcessingQueue::CANDIDATE_ID;
    }

    public function processingRuleId()
    {
        return ServiceProcessingQueue::PROCESSING_RULE_ID;
    }

    public function status()
    {
        return ServiceProcessingQueue::STATUS;
    }

    public function attempts()
    {
        return ServiceProcessingQueue::ATTEMPTS;
    }

    public function maxAttempts()
    {
        return ServiceProcessingQueue::MAX_ATTEMPTS;
    }

    public function requestData()
    {
        return ServiceProcessingQueue::REQUEST_DATA;
    }

    public function responseData()
    {
        return ServiceProcessingQueue::RESPONSE_DATA;
    }

    public function errorMessage()
    {
        return ServiceProcessingQueue::ERROR_MESSAGE;
    }

    public function processedAt()
    {
        return ServiceProcessingQueue::PROCESSED_AT;
    }

    public function nextRetryAt()
    {
        return ServiceProcessingQueue::NEXT_RETRY_AT;
    }

    public function completedAt()
    {
        return ServiceProcessingQueue::COMPLETED_AT;
    }

    public function createdBy()
    {
        return ServiceProcessingQueue::CREATED_BY;
    }

    public function updatedBy()
    {
        return ServiceProcessingQueue::UPDATED_BY;
    }
    // functions
}
