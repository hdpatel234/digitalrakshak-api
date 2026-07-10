<?php

namespace App\Services;

use App\Repositories\PaymentTransactionRepository;

/**
 * @property PaymentTransactionRepository $repository
 */
class PaymentTransactionService extends BaseService
{
    public function __construct(PaymentTransactionRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function transactionUuid()
    {
        return $this->repository->transactionUuid();
    }

    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function orderId()
    {
        return $this->repository->orderId();
    }

    public function invoiceId()
    {
        return $this->repository->invoiceId();
    }

    public function gatewayConfigId()
    {
        return $this->repository->gatewayConfigId();
    }

    public function clientGatewayId()
    {
        return $this->repository->clientGatewayId();
    }

    public function methodTypeId()
    {
        return $this->repository->methodTypeId();
    }

    public function amount()
    {
        return $this->repository->amount();
    }

    public function currency()
    {
        return $this->repository->currency();
    }

    public function taxAmount()
    {
        return $this->repository->taxAmount();
    }

    public function feeAmount()
    {
        return $this->repository->feeAmount();
    }

    public function netAmount()
    {
        return $this->repository->netAmount();
    }

    public function gatewayTransactionId()
    {
        return $this->repository->gatewayTransactionId();
    }

    public function gatewayOrderId()
    {
        return $this->repository->gatewayOrderId();
    }

    public function gatewayPaymentId()
    {
        return $this->repository->gatewayPaymentId();
    }

    public function bankReference()
    {
        return $this->repository->bankReference();
    }

    public function paymentMethod()
    {
        return $this->repository->paymentMethod();
    }

    public function paymentDetails()
    {
        return $this->repository->paymentDetails();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function paymentStatus()
    {
        return $this->repository->paymentStatus();
    }

    public function initiatedAt()
    {
        return $this->repository->initiatedAt();
    }

    public function authorizedAt()
    {
        return $this->repository->authorizedAt();
    }

    public function capturedAt()
    {
        return $this->repository->capturedAt();
    }

    public function successAt()
    {
        return $this->repository->successAt();
    }

    public function failedAt()
    {
        return $this->repository->failedAt();
    }

    public function refundedAt()
    {
        return $this->repository->refundedAt();
    }

    public function gatewayRequest()
    {
        return $this->repository->gatewayRequest();
    }

    public function gatewayResponse()
    {
        return $this->repository->gatewayResponse();
    }

    public function gatewayWebhook()
    {
        return $this->repository->gatewayWebhook();
    }

    public function errorCode()
    {
        return $this->repository->errorCode();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }

    public function refundAmount()
    {
        return $this->repository->refundAmount();
    }

    public function refundReason()
    {
        return $this->repository->refundReason();
    }

    public function refundTransactionId()
    {
        return $this->repository->refundTransactionId();
    }

    public function ipAddress()
    {
        return $this->repository->ipAddress();
    }

    public function userAgent()
    {
        return $this->repository->userAgent();
    }

    public function customerName()
    {
        return $this->repository->customerName();
    }

    public function customerEmail()
    {
        return $this->repository->customerEmail();
    }

    public function customerPhone()
    {
        return $this->repository->customerPhone();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }
    // functions
}
