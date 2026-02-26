<?php

namespace App\Repositories;

use App\Models\CandidateOrder;

class CandidateOrderRepository extends BaseRepository
{
    public function __construct(CandidateOrder $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function orderNumber()
    {
        return $this->model::ORDER_NUMBER;
    }

    public function clientOrderNumber()
    {
        return $this->model::CLIENT_ORDER_NUMBER;
    }

    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function billingConfigId()
    {
        return $this->model::BILLING_CONFIG_ID;
    }

    public function invoiceId()
    {
        return $this->model::INVOICE_ID;
    }

    public function billingSyncStatus()
    {
        return $this->model::BILLING_SYNC_STATUS;
    }

    public function billingSyncMessage()
    {
        return $this->model::BILLING_SYNC_MESSAGE;
    }

    public function packageId()
    {
        return $this->model::PACKAGE_ID;
    }

    public function orderType()
    {
        return $this->model::ORDER_TYPE;
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

    public function taxPercentage()
    {
        return $this->model::TAX_PERCENTAGE;
    }

    public function totalAmount()
    {
        return $this->model::TOTAL_AMOUNT;
    }

    public function paymentStatus()
    {
        return $this->model::PAYMENT_STATUS;
    }

    public function paymentMethod()
    {
        return $this->model::PAYMENT_METHOD;
    }

    public function paymentReference()
    {
        return $this->model::PAYMENT_REFERENCE;
    }

    public function paymentDueDate()
    {
        return $this->model::PAYMENT_DUE_DATE;
    }

    public function invoiceNumber()
    {
        return $this->model::INVOICE_NUMBER;
    }

    public function invoiceGeneratedAt()
    {
        return $this->model::INVOICE_GENERATED_AT;
    }

    public function notes()
    {
        return $this->model::NOTES;
    }

    public function internalNotes()
    {
        return $this->model::INTERNAL_NOTES;
    }

    public function orderDate()
    {
        return $this->model::ORDER_DATE;
    }

    public function processedAt()
    {
        return $this->model::PROCESSED_AT;
    }

    public function completedAt()
    {
        return $this->model::COMPLETED_AT;
    }

    public function cancelledAt()
    {
        return $this->model::CANCELLED_AT;
    }

    public function cancellationReason()
    {
        return $this->model::CANCELLATION_REASON;
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