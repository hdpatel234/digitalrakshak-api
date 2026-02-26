<?php

namespace App\Services;

use App\Repositories\PaymentTransactionRepository;

class PaymentTransactionService extends BaseService
{
    protected $repository;
    
    public function __construct(PaymentTransactionRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function invoiceId()
    {
        return $this->repository->invoiceId();
    }

    public function orderId()
    {
        return $this->repository->orderId();
    }

    public function billingConfigId()
    {
        return $this->repository->billingConfigId();
    }

    public function externalTransactionId()
    {
        return $this->repository->externalTransactionId();
    }

    public function transactionReference()
    {
        return $this->repository->transactionReference();
    }

    public function paymentMethod()
    {
        return $this->repository->paymentMethod();
    }

    public function amount()
    {
        return $this->repository->amount();
    }

    public function currency()
    {
        return $this->repository->currency();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function transactionDate()
    {
        return $this->repository->transactionDate();
    }

    public function gatewayResponse()
    {
        return $this->repository->gatewayResponse();
    }

    public function refundAmount()
    {
        return $this->repository->refundAmount();
    }

    public function refundReason()
    {
        return $this->repository->refundReason();
    }

    public function refundDate()
    {
        return $this->repository->refundDate();
    }

    public function syncStatus()
    {
        return $this->repository->syncStatus();
    }

    public function syncMessage()
    {
        return $this->repository->syncMessage();
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