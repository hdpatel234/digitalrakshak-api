<?php

namespace App\Repositories;

use App\Models\Invoice;

class InvoiceRepository extends BaseRepository
{
    public function __construct(Invoice $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function orderId()
    {
        return $this->model::ORDER_ID;
    }

    public function billingConfigId()
    {
        return $this->model::BILLING_CONFIG_ID;
    }

    public function externalInvoiceId()
    {
        return $this->model::EXTERNAL_INVOICE_ID;
    }

    public function externalInvoiceNumber()
    {
        return $this->model::EXTERNAL_INVOICE_NUMBER;
    }

    public function invoiceNumber()
    {
        return $this->model::INVOICE_NUMBER;
    }

    public function invoiceDate()
    {
        return $this->model::INVOICE_DATE;
    }

    public function dueDate()
    {
        return $this->model::DUE_DATE;
    }

    public function subtotal()
    {
        return $this->model::SUBTOTAL;
    }

    public function discountAmount()
    {
        return $this->model::DISCOUNT_AMOUNT;
    }

    public function taxAmount()
    {
        return $this->model::TAX_AMOUNT;
    }

    public function taxPercentage()
    {
        return $this->model::TAX_PERCENTAGE;
    }

    public function totalAmount()
    {
        return $this->model::TOTAL_AMOUNT;
    }

    public function amountPaid()
    {
        return $this->model::AMOUNT_PAID;
    }

    public function amountDue()
    {
        return $this->model::AMOUNT_DUE;
    }

    public function currency()
    {
        return $this->model::CURRENCY;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function paymentStatus()
    {
        return $this->model::PAYMENT_STATUS;
    }

    public function pdfUrl()
    {
        return $this->model::PDF_URL;
    }

    public function syncStatus()
    {
        return $this->model::SYNC_STATUS;
    }

    public function syncMessage()
    {
        return $this->model::SYNC_MESSAGE;
    }

    public function lastSyncAt()
    {
        return $this->model::LAST_SYNC_AT;
    }

    public function invoiceData()
    {
        return $this->model::INVOICE_DATA;
    }

    public function documentId()
    {
        return $this->model::DOCUMENT_ID;
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