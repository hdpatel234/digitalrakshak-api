<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransaction extends BaseModel
{
    
    protected $table = "payment_transactions";
    
    const CLIENT_ID = "client_id";
    const INVOICE_ID = "invoice_id";
    const ORDER_ID = "order_id";
    const BILLING_CONFIG_ID = "billing_config_id";
    const EXTERNAL_TRANSACTION_ID = "external_transaction_id";
    const TRANSACTION_REFERENCE = "transaction_reference";
    const PAYMENT_METHOD = "payment_method";
    const AMOUNT = "amount";
    const CURRENCY = "currency";
    const STATUS = "status";
    const TRANSACTION_DATE = "transaction_date";
    const GATEWAY_RESPONSE = "gateway_response";
    const REFUND_AMOUNT = "refund_amount";
    const REFUND_REASON = "refund_reason";
    const REFUND_DATE = "refund_date";
    const SYNC_STATUS = "sync_status";
    const SYNC_MESSAGE = "sync_message";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::CLIENT_ID,
        self::INVOICE_ID,
        self::ORDER_ID,
        self::BILLING_CONFIG_ID,
        self::EXTERNAL_TRANSACTION_ID,
        self::TRANSACTION_REFERENCE,
        self::PAYMENT_METHOD,
        self::AMOUNT,
        self::CURRENCY,
        self::STATUS,
        self::TRANSACTION_DATE,
        self::GATEWAY_RESPONSE,
        self::REFUND_AMOUNT,
        self::REFUND_REASON,
        self::REFUND_DATE,
        self::SYNC_STATUS,
        self::SYNC_MESSAGE,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}