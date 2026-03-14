<?php

namespace App\Services;

use App\Repositories\PaymentRefundRepository;

class PaymentRefundService extends BaseService
{
    public function __construct(PaymentRefundRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function transactionId()
    {
        return $this->repository->transactionId();
    }

    public function refundUuid()
    {
        return $this->repository->refundUuid();
    }

    public function gatewayRefundId()
    {
        return $this->repository->gatewayRefundId();
    }

    public function amount()
    {
        return $this->repository->amount();
    }

    public function reason()
    {
        return $this->repository->reason();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function gatewayRequest()
    {
        return $this->repository->gatewayRequest();
    }

    public function gatewayResponse()
    {
        return $this->repository->gatewayResponse();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }

    public function initiatedBy()
    {
        return $this->repository->initiatedBy();
    }

    public function approvedBy()
    {
        return $this->repository->approvedBy();
    }

    public function approvedAt()
    {
        return $this->repository->approvedAt();
    }
    // functions
}
