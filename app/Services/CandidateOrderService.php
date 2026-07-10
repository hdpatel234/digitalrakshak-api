<?php

namespace App\Services;

use App\Repositories\CandidateOrderRepository;

/**
 * @property CandidateOrderRepository $repository
 */
class CandidateOrderService extends BaseService
{
    
    public function __construct(CandidateOrderRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function orderNumber()
    {
        return $this->repository->orderNumber();
    }

    public function clientOrderNumber()
    {
        return $this->repository->clientOrderNumber();
    }

    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function billingConfigId()
    {
        return $this->repository->billingConfigId();
    }

    public function invoiceId()
    {
        return $this->repository->invoiceId();
    }

    public function billingSyncStatus()
    {
        return $this->repository->billingSyncStatus();
    }

    public function billingSyncMessage()
    {
        return $this->repository->billingSyncMessage();
    }

    public function packageId()
    {
        return $this->repository->packageId();
    }

    public function orderType()
    {
        return $this->repository->orderType();
    }

    public function subtotal()
    {
        return $this->repository->subtotal();
    }

    public function discountAmount()
    {
        return $this->repository->discountAmount();
    }

    public function taxAmount()
    {
        return $this->repository->taxAmount();
    }

    public function taxPercentage()
    {
        return $this->repository->taxPercentage();
    }

    public function totalAmount()
    {
        return $this->repository->totalAmount();
    }

    public function paymentStatus()
    {
        return $this->repository->paymentStatus();
    }

    public function paymentMethod()
    {
        return $this->repository->paymentMethod();
    }

    public function paymentReference()
    {
        return $this->repository->paymentReference();
    }

    public function paymentDueDate()
    {
        return $this->repository->paymentDueDate();
    }

    public function invoiceNumber()
    {
        return $this->repository->invoiceNumber();
    }

    public function invoiceGeneratedAt()
    {
        return $this->repository->invoiceGeneratedAt();
    }

    public function notes()
    {
        return $this->repository->notes();
    }

    public function internalNotes()
    {
        return $this->repository->internalNotes();
    }

    public function orderDate()
    {
        return $this->repository->orderDate();
    }

    public function processedAt()
    {
        return $this->repository->processedAt();
    }

    public function completedAt()
    {
        return $this->repository->completedAt();
    }

    public function cancelledAt()
    {
        return $this->repository->cancelledAt();
    }

    public function cancellationReason()
    {
        return $this->repository->cancellationReason();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }

    public function deletedBy()
    {
        return $this->repository->deletedBy();
    }
    // functions
}