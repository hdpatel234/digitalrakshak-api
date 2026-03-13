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
    public function transactionUuid()
    {
        return $this->model::TRANSACTION_UUID;
    }

    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function orderId()
    {
        return $this->model::ORDER_ID;
    }

    public function invoiceId()
    {
        return $this->model::INVOICE_ID;
    }

    public function gatewayConfigId()
    {
        return $this->model::GATEWAY_CONFIG_ID;
    }

    public function clientGatewayId()
    {
        return $this->model::CLIENT_GATEWAY_ID;
    }

    public function methodTypeId()
    {
        return $this->model::METHOD_TYPE_ID;
    }

    public function amount()
    {
        return $this->model::AMOUNT;
    }

    public function currency()
    {
        return $this->model::CURRENCY;
    }

    public function taxAmount()
    {
        return $this->model::TAX_AMOUNT;
    }

    public function feeAmount()
    {
        return $this->model::FEE_AMOUNT;
    }

    public function netAmount()
    {
        return $this->model::NET_AMOUNT;
    }

    public function gatewayTransactionId()
    {
        return $this->model::GATEWAY_TRANSACTION_ID;
    }

    public function gatewayOrderId()
    {
        return $this->model::GATEWAY_ORDER_ID;
    }

    public function gatewayPaymentId()
    {
        return $this->model::GATEWAY_PAYMENT_ID;
    }

    public function bankReference()
    {
        return $this->model::BANK_REFERENCE;
    }

    public function paymentMethod()
    {
        return $this->model::PAYMENT_METHOD;
    }

    public function paymentDetails()
    {
        return $this->model::PAYMENT_DETAILS;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function paymentStatus()
    {
        return $this->model::PAYMENT_STATUS;
    }

    public function initiatedAt()
    {
        return $this->model::INITIATED_AT;
    }

    public function authorizedAt()
    {
        return $this->model::AUTHORIZED_AT;
    }

    public function capturedAt()
    {
        return $this->model::CAPTURED_AT;
    }

    public function successAt()
    {
        return $this->model::SUCCESS_AT;
    }

    public function failedAt()
    {
        return $this->model::FAILED_AT;
    }

    public function refundedAt()
    {
        return $this->model::REFUNDED_AT;
    }

    public function gatewayRequest()
    {
        return $this->model::GATEWAY_REQUEST;
    }

    public function gatewayResponse()
    {
        return $this->model::GATEWAY_RESPONSE;
    }

    public function gatewayWebhook()
    {
        return $this->model::GATEWAY_WEBHOOK;
    }

    public function errorCode()
    {
        return $this->model::ERROR_CODE;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }

    public function refundAmount()
    {
        return $this->model::REFUND_AMOUNT;
    }

    public function refundReason()
    {
        return $this->model::REFUND_REASON;
    }

    public function refundTransactionId()
    {
        return $this->model::REFUND_TRANSACTION_ID;
    }

    public function ipAddress()
    {
        return $this->model::IP_ADDRESS;
    }

    public function userAgent()
    {
        return $this->model::USER_AGENT;
    }

    public function customerName()
    {
        return $this->model::CUSTOMER_NAME;
    }

    public function customerEmail()
    {
        return $this->model::CUSTOMER_EMAIL;
    }

    public function customerPhone()
    {
        return $this->model::CUSTOMER_PHONE;
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