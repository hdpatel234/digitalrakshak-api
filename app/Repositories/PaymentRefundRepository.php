<?php

namespace App\Repositories;

use App\Models\PaymentRefund;

class PaymentRefundRepository extends BaseRepository
{
    public function __construct(PaymentRefund $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function transactionId()
    {
        return PaymentRefund::TRANSACTION_ID;
    }

    public function gatewayRefundId()
    {
        return PaymentRefund::GATEWAY_REFUND_ID;
    }

    public function amount()
    {
        return PaymentRefund::AMOUNT;
    }

    public function reason()
    {
        return PaymentRefund::REASON;
    }

    public function status()
    {
        return PaymentRefund::STATUS;
    }

    public function gatewayRequest()
    {
        return PaymentRefund::GATEWAY_REQUEST;
    }

    public function gatewayResponse()
    {
        return PaymentRefund::GATEWAY_RESPONSE;
    }

    public function errorMessage()
    {
        return PaymentRefund::ERROR_MESSAGE;
    }

    public function initiatedBy()
    {
        return PaymentRefund::INITIATED_BY;
    }

    public function approvedBy()
    {
        return PaymentRefund::APPROVED_BY;
    }

    public function approvedAt()
    {
        return PaymentRefund::APPROVED_AT;
    }

    // functions
}
