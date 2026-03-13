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
        return $this->model::TRANSACTION_ID;
    }

    public function refundUuid()
    {
        return $this->model::REFUND_UUID;
    }

    public function gatewayRefundId()
    {
        return $this->model::GATEWAY_REFUND_ID;
    }

    public function amount()
    {
        return $this->model::AMOUNT;
    }

    public function reason()
    {
        return $this->model::REASON;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function gatewayRequest()
    {
        return $this->model::GATEWAY_REQUEST;
    }

    public function gatewayResponse()
    {
        return $this->model::GATEWAY_RESPONSE;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }

    public function initiatedBy()
    {
        return $this->model::INITIATED_BY;
    }

    public function approvedBy()
    {
        return $this->model::APPROVED_BY;
    }

    public function approvedAt()
    {
        return $this->model::APPROVED_AT;
    }
    // functions
}