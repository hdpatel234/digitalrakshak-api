<?php

namespace App\Repositories;

use App\Models\PaymentTransaction;

class PaymentTransactionRepository extends BaseRepository
{
    public function __construct(PaymentTransaction $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function invoiceId()
    {
        return $this->model::INVOICE_ID;
    }

    public function orderId()
    {
        return $this->model::ORDER_ID;
    }

    public function billingConfigId()
    {
        return $this->model::BILLING_CONFIG_ID;
    }

    public function externalTransactionId()
    {
        return $this->model::EXTERNAL_TRANSACTION_ID;
    }

    public function transactionReference()
    {
        return $this->model::TRANSACTION_REFERENCE;
    }

    public function paymentMethod()
    {
        return $this->model::PAYMENT_METHOD;
    }

    public function amount()
    {
        return $this->model::AMOUNT;
    }

    public function currency()
    {
        return $this->model::CURRENCY;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function transactionDate()
    {
        return $this->model::TRANSACTION_DATE;
    }

    public function gatewayResponse()
    {
        return $this->model::GATEWAY_RESPONSE;
    }

    public function refundAmount()
    {
        return $this->model::REFUND_AMOUNT;
    }

    public function refundReason()
    {
        return $this->model::REFUND_REASON;
    }

    public function refundDate()
    {
        return $this->model::REFUND_DATE;
    }

    public function syncStatus()
    {
        return $this->model::SYNC_STATUS;
    }

    public function syncMessage()
    {
        return $this->model::SYNC_MESSAGE;
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