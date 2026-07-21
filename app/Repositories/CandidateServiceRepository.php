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
        return CandidateService::CANDIDATE_ID;
    }

    public function serviceId()
    {
        return CandidateService::SERVICE_ID;
    }

    public function orderId()
    {
        return CandidateService::ORDER_ID;
    }

    public function orderItemId()
    {
        return CandidateService::ORDER_ITEM_ID;
    }

    public function pricePaid()
    {
        return CandidateService::PRICE_PAID;
    }

    public function processingRuleId()
    {
        return CandidateService::PROCESSING_RULE_ID;
    }

    public function processingStatus()
    {
        return CandidateService::PROCESSING_STATUS;
    }

    public function processingAttempts()
    {
        return CandidateService::PROCESSING_ATTEMPTS;
    }

    public function processedAt()
    {
        return CandidateService::PROCESSED_AT;
    }

    public function completedAt()
    {
        return CandidateService::COMPLETED_AT;
    }

    public function errorMessage()
    {
        return CandidateService::ERROR_MESSAGE;
    }

    public function status()
    {
        return CandidateService::STATUS;
    }

    public function createdBy()
    {
        return CandidateService::CREATED_BY;
    }

    public function updatedBy()
    {
        return CandidateService::UPDATED_BY;
    }

    public function deletedBy()
    {
        return CandidateService::DELETED_BY;
    }
    // functions
}
