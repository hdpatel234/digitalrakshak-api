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
        return PaymentTransaction::CLIENT_ID;
    }

    public function orderId()
    {
        return PaymentTransaction::ORDER_ID;
    }

    public function invoiceId()
    {
        return PaymentTransaction::INVOICE_ID;
    }

    public function gatewayConfigId()
    {
        return PaymentTransaction::GATEWAY_CONFIG_ID;
    }

    public function methodTypeId()
    {
        return PaymentTransaction::METHOD_TYPE_ID;
    }

    public function amount()
    {
        return PaymentTransaction::AMOUNT;
    }

    public function currency()
    {
        return PaymentTransaction::CURRENCY;
    }

    public function taxAmount()
    {
        return PaymentTransaction::TAX_AMOUNT;
    }

    public function feeAmount()
    {
        return PaymentTransaction::FEE_AMOUNT;
    }

    public function netAmount()
    {
        return PaymentTransaction::NET_AMOUNT;
    }

    public function gatewayTransactionId()
    {
        return PaymentTransaction::GATEWAY_TRANSACTION_ID;
    }

    public function gatewayOrderId()
    {
        return PaymentTransaction::GATEWAY_ORDER_ID;
    }

    public function gatewayPaymentId()
    {
        return PaymentTransaction::GATEWAY_PAYMENT_ID;
    }

    public function paymentDetails()
    {
        return PaymentTransaction::PAYMENT_DETAILS;
    }

    public function status()
    {
        return PaymentTransaction::STATUS;
    }

    public function paymentStatus()
    {
        return PaymentTransaction::PAYMENT_STATUS;
    }

    public function initiatedAt()
    {
        return PaymentTransaction::INITIATED_AT;
    }

    public function authorizedAt()
    {
        return PaymentTransaction::AUTHORIZED_AT;
    }

    public function capturedAt()
    {
        return PaymentTransaction::CAPTURED_AT;
    }

    public function successAt()
    {
        return PaymentTransaction::SUCCESS_AT;
    }

    public function failedAt()
    {
        return PaymentTransaction::FAILED_AT;
    }

    public function refundedAt()
    {
        return PaymentTransaction::REFUNDED_AT;
    }

    public function gatewayRequest()
    {
        return PaymentTransaction::GATEWAY_REQUEST;
    }

    public function gatewayResponse()
    {
        return PaymentTransaction::GATEWAY_RESPONSE;
    }

    public function errorCode()
    {
        return PaymentTransaction::ERROR_CODE;
    }

    public function errorMessage()
    {
        return PaymentTransaction::ERROR_MESSAGE;
    }

    public function ipAddress()
    {
        return PaymentTransaction::IP_ADDRESS;
    }

    public function userAgent()
    {
        return PaymentTransaction::USER_AGENT;
    }

    // functions
}
