<?php

namespace App\Repositories;

use App\Models\OrderItem;

class OrderItemRepository extends BaseRepository
{
    public function __construct(OrderItem $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function orderId()
    {
        return $this->model::ORDER_ID;
    }

    public function orderCandidateId()
    {
        return $this->model::ORDER_CANDIDATE_ID;
    }

    public function serviceId()
    {
        return $this->model::SERVICE_ID;
    }

    public function supportConfigId()
    {
        return $this->model::SUPPORT_CONFIG_ID;
    }

    public function ticketId()
    {
        return $this->model::TICKET_ID;
    }

    public function supportSyncStatus()
    {
        return $this->model::SUPPORT_SYNC_STATUS;
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

    public function unitPrice()
    {
        return $this->model::UNIT_PRICE;
    }

    public function quantity()
    {
        return $this->model::QUANTITY;
    }

    public function discountAmount()
    {
        return $this->model::DISCOUNT_AMOUNT;
    }

    public function taxAmount()
    {
        return $this->model::TAX_AMOUNT;
    }

    public function taxPercentage()
    {
        return $this->model::TAX_PERCENTAGE;
    }

    public function totalPrice()
    {
        return $this->model::TOTAL_PRICE;
    }

    public function serviceData()
    {
        return $this->model::SERVICE_DATA;
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