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
        return Invoice::CLIENT_ID;
    }

    public function orderId()
    {
        return Invoice::ORDER_ID;
    }

    public function billingConfigId()
    {
        return Invoice::BILLING_CONFIG_ID;
    }

    public function externalInvoiceId()
    {
        return Invoice::EXTERNAL_INVOICE_ID;
    }

    public function externalInvoiceNumber()
    {
        return Invoice::EXTERNAL_INVOICE_NUMBER;
    }

    public function invoiceNumber()
    {
        return Invoice::INVOICE_NUMBER;
    }

    public function invoiceDate()
    {
        return Invoice::INVOICE_DATE;
    }

    public function dueDate()
    {
        return Invoice::DUE_DATE;
    }

    public function subtotal()
    {
        return Invoice::SUBTOTAL;
    }

    public function discountAmount()
    {
        return Invoice::DISCOUNT_AMOUNT;
    }

    public function taxAmount()
    {
        return Invoice::TAX_AMOUNT;
    }

    public function taxPercentage()
    {
        return Invoice::TAX_PERCENTAGE;
    }

    public function totalAmount()
    {
        return Invoice::TOTAL_AMOUNT;
    }

    public function amountPaid()
    {
        return Invoice::AMOUNT_PAID;
    }

    public function amountDue()
    {
        return Invoice::AMOUNT_DUE;
    }

    public function currency()
    {
        return Invoice::CURRENCY;
    }

    public function status()
    {
        return Invoice::STATUS;
    }

    public function paymentStatus()
    {
        return Invoice::PAYMENT_STATUS;
    }

    public function pdfUrl()
    {
        return Invoice::PDF_URL;
    }

    public function syncStatus()
    {
        return Invoice::SYNC_STATUS;
    }

    public function syncMessage()
    {
        return Invoice::SYNC_MESSAGE;
    }

    public function lastSyncAt()
    {
        return Invoice::LAST_SYNC_AT;
    }

    public function invoiceData()
    {
        return Invoice::INVOICE_DATA;
    }

    public function documentId()
    {
        return Invoice::DOCUMENT_ID;
    }

    public function createdBy()
    {
        return Invoice::CREATED_BY;
    }

    public function updatedBy()
    {
        return Invoice::UPDATED_BY;
    }
    // functions
}