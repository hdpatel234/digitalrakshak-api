<?php

namespace App\Services;

use App\Repositories\InvoiceRepository;

class InvoiceService extends BaseService
{
    protected $repository;
    
    public function __construct(InvoiceRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function orderId()
    {
        return $this->repository->orderId();
    }

    public function billingConfigId()
    {
        return $this->repository->billingConfigId();
    }

    public function externalInvoiceId()
    {
        return $this->repository->externalInvoiceId();
    }

    public function externalInvoiceNumber()
    {
        return $this->repository->externalInvoiceNumber();
    }

    public function invoiceNumber()
    {
        return $this->repository->invoiceNumber();
    }

    public function invoiceDate()
    {
        return $this->repository->invoiceDate();
    }

    public function dueDate()
    {
        return $this->repository->dueDate();
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

    public function amountPaid()
    {
        return $this->repository->amountPaid();
    }

    public function amountDue()
    {
        return $this->repository->amountDue();
    }

    public function currency()
    {
        return $this->repository->currency();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function paymentStatus()
    {
        return $this->repository->paymentStatus();
    }

    public function pdfUrl()
    {
        return $this->repository->pdfUrl();
    }

    public function syncStatus()
    {
        return $this->repository->syncStatus();
    }

    public function syncMessage()
    {
        return $this->repository->syncMessage();
    }

    public function lastSyncAt()
    {
        return $this->repository->lastSyncAt();
    }

    public function invoiceData()
    {
        return $this->repository->invoiceData();
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