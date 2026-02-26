<?php

namespace App\Services;

use App\Repositories\InvoiceItemRepository;

class InvoiceItemService extends BaseService
{
    protected $repository;
    
    public function __construct(InvoiceItemRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function invoiceId()
    {
        return $this->repository->invoiceId();
    }

    public function orderItemId()
    {
        return $this->repository->orderItemId();
    }

    public function itemType()
    {
        return $this->repository->itemType();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function quantity()
    {
        return $this->repository->quantity();
    }

    public function unitPrice()
    {
        return $this->repository->unitPrice();
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

    public function totalPrice()
    {
        return $this->repository->totalPrice();
    }

    public function externalItemId()
    {
        return $this->repository->externalItemId();
    }

    public function itemData()
    {
        return $this->repository->itemData();
    }
    // functions
}