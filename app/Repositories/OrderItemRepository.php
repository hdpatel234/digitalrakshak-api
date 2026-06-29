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
        return OrderItem::ORDER_ID;
    }

    public function orderCandidateId()
    {
        return OrderItem::ORDER_CANDIDATE_ID;
    }

    public function serviceId()
    {
        return OrderItem::SERVICE_ID;
    }

    public function supportConfigId()
    {
        return OrderItem::SUPPORT_CONFIG_ID;
    }

    public function ticketId()
    {
        return OrderItem::TICKET_ID;
    }
    
    public function reportDocumentId()
    {
        return OrderItem::REPORT_DOCUMENT_ID;
    }

    public function supportSyncStatus()
    {
        return OrderItem::SUPPORT_SYNC_STATUS;
    }

    public function processingRuleId()
    {
        return OrderItem::PROCESSING_RULE_ID;
    }

    public function processingStatus()
    {
        return OrderItem::PROCESSING_STATUS;
    }

    public function processingAttempts()
    {
        return OrderItem::PROCESSING_ATTEMPTS;
    }

    public function processedAt()
    {
        return OrderItem::PROCESSED_AT;
    }

    public function completedAt()
    {
        return OrderItem::COMPLETED_AT;
    }

    public function errorMessage()
    {
        return OrderItem::ERROR_MESSAGE;
    }

    public function unitPrice()
    {
        return OrderItem::UNIT_PRICE;
    }

    public function quantity()
    {
        return OrderItem::QUANTITY;
    }

    public function discountAmount()
    {
        return OrderItem::DISCOUNT_AMOUNT;
    }

    public function taxAmount()
    {
        return OrderItem::TAX_AMOUNT;
    }

    public function taxPercentage()
    {
        return OrderItem::TAX_PERCENTAGE;
    }

    public function totalPrice()
    {
        return OrderItem::TOTAL_PRICE;
    }

    public function serviceData()
    {
        return OrderItem::SERVICE_DATA;
    }

    public function status()
    {
        return OrderItem::STATUS;
    }

    public function createdBy()
    {
        return OrderItem::CREATED_BY;
    }

    public function updatedBy()
    {
        return OrderItem::UPDATED_BY;
    }

    public function deletedBy()
    {
        return OrderItem::DELETED_BY;
    }
    // functions
}