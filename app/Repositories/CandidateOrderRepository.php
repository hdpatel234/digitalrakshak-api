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
        return CandidateOrder::ORDER_NUMBER;
    }

    public function clientOrderNumber()
    {
        return CandidateOrder::CLIENT_ORDER_NUMBER;
    }

    public function clientId()
    {
        return CandidateOrder::CLIENT_ID;
    }

    public function billingConfigId()
    {
        return CandidateOrder::BILLING_CONFIG_ID;
    }

    public function invoiceId()
    {
        return CandidateOrder::INVOICE_ID;
    }

    public function billingSyncStatus()
    {
        return CandidateOrder::BILLING_SYNC_STATUS;
    }

    public function billingSyncMessage()
    {
        return CandidateOrder::BILLING_SYNC_MESSAGE;
    }

    public function packageId()
    {
        return CandidateOrder::PACKAGE_ID;
    }

    public function orderType()
    {
        return CandidateOrder::ORDER_TYPE;
    }

    public function subtotal()
    {
        return CandidateOrder::SUBTOTAL;
    }

    public function discountAmount()
    {
        return CandidateOrder::DISCOUNT_AMOUNT;
    }

    public function taxAmount()
    {
        return CandidateOrder::TAX_AMOUNT;
    }

    public function taxPercentage()
    {
        return CandidateOrder::TAX_PERCENTAGE;
    }

    public function totalAmount()
    {
        return CandidateOrder::TOTAL_AMOUNT;
    }

    public function paymentStatus()
    {
        return CandidateOrder::PAYMENT_STATUS;
    }

    public function paymentMethod()
    {
        return CandidateOrder::PAYMENT_METHOD;
    }

    public function paymentReference()
    {
        return CandidateOrder::PAYMENT_REFERENCE;
    }

    public function paymentDueDate()
    {
        return CandidateOrder::PAYMENT_DUE_DATE;
    }

    public function invoiceNumber()
    {
        return CandidateOrder::INVOICE_NUMBER;
    }

    public function invoiceGeneratedAt()
    {
        return CandidateOrder::INVOICE_GENERATED_AT;
    }

    public function notes()
    {
        return CandidateOrder::NOTES;
    }

    public function internalNotes()
    {
        return CandidateOrder::INTERNAL_NOTES;
    }

    public function orderDate()
    {
        return CandidateOrder::ORDER_DATE;
    }

    public function processedAt()
    {
        return CandidateOrder::PROCESSED_AT;
    }

    public function completedAt()
    {
        return CandidateOrder::COMPLETED_AT;
    }

    public function cancelledAt()
    {
        return CandidateOrder::CANCELLED_AT;
    }

    public function cancellationReason()
    {
        return CandidateOrder::CANCELLATION_REASON;
    }

    public function status()
    {
        return CandidateOrder::STATUS;
    }

    public function createdBy()
    {
        return CandidateOrder::CREATED_BY;
    }

    public function updatedBy()
    {
        return CandidateOrder::UPDATED_BY;
    }

    public function deletedBy()
    {
        return CandidateOrder::DELETED_BY;
    }
    
    // functions
    public function getTotalRevenue(string $paymentStatus)
    {
        return $this->query()->where($this->paymentStatus(), $paymentStatus)->sum($this->totalAmount());
    }

    public function countBetweenDates($start, $end)
    {
        return $this->query()->whereBetween($this->createdAt(), [$start, $end])->count();
    }

    public function getRecentOrders(int $limit)
    {
        return $this->query()->with(['client', 'candidates'])->orderBy($this->createdAt(), 'desc')->limit($limit)->get();
    }
}