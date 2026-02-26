<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends BaseModel
{
    
    protected $table = "invoices";
    
    const CLIENT_ID = "client_id";
    const ORDER_ID = "order_id";
    const BILLING_CONFIG_ID = "billing_config_id";
    const EXTERNAL_INVOICE_ID = "external_invoice_id";
    const EXTERNAL_INVOICE_NUMBER = "external_invoice_number";
    const INVOICE_NUMBER = "invoice_number";
    const INVOICE_DATE = "invoice_date";
    const DUE_DATE = "due_date";
    const SUBTOTAL = "subtotal";
    const DISCOUNT_AMOUNT = "discount_amount";
    const TAX_AMOUNT = "tax_amount";
    const TAX_PERCENTAGE = "tax_percentage";
    const TOTAL_AMOUNT = "total_amount";
    const AMOUNT_PAID = "amount_paid";
    const AMOUNT_DUE = "amount_due";
    const CURRENCY = "currency";
    const STATUS = "status";
    const PAYMENT_STATUS = "payment_status";
    const PDF_URL = "pdf_url";
    const SYNC_STATUS = "sync_status";
    const SYNC_MESSAGE = "sync_message";
    const LAST_SYNC_AT = "last_sync_at";
    const INVOICE_DATA = "invoice_data";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::ORDER_ID,
        self::BILLING_CONFIG_ID,
        self::EXTERNAL_INVOICE_ID,
        self::EXTERNAL_INVOICE_NUMBER,
        self::INVOICE_NUMBER,
        self::INVOICE_DATE,
        self::DUE_DATE,
        self::SUBTOTAL,
        self::DISCOUNT_AMOUNT,
        self::TAX_AMOUNT,
        self::TAX_PERCENTAGE,
        self::TOTAL_AMOUNT,
        self::AMOUNT_PAID,
        self::AMOUNT_DUE,
        self::CURRENCY,
        self::STATUS,
        self::PAYMENT_STATUS,
        self::PDF_URL,
        self::SYNC_STATUS,
        self::SYNC_MESSAGE,
        self::LAST_SYNC_AT,
        self::INVOICE_DATA,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}