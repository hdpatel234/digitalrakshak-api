<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateOrder extends BaseModel
{
    
    protected $table = "candidate_orders";
    
    const ORDER_NUMBER = "order_number";
    const CLIENT_ORDER_NUMBER = "client_order_number";
    const CLIENT_ID = "client_id";
    const BILLING_CONFIG_ID = "billing_config_id";
    const INVOICE_ID = "invoice_id";
    const BILLING_SYNC_STATUS = "billing_sync_status";
    const BILLING_SYNC_MESSAGE = "billing_sync_message";
    const PACKAGE_ID = "package_id";
    const ORDER_TYPE = "order_type";
    const SUBTOTAL = "subtotal";
    const DISCOUNT_AMOUNT = "discount_amount";
    const TAX_AMOUNT = "tax_amount";
    const TAX_PERCENTAGE = "tax_percentage";
    const TOTAL_AMOUNT = "total_amount";
    const PAYMENT_STATUS = "payment_status";
    const PAYMENT_METHOD = "payment_method";
    const PAYMENT_REFERENCE = "payment_reference";
    const PAYMENT_DUE_DATE = "payment_due_date";
    const INVOICE_NUMBER = "invoice_number";
    const INVOICE_GENERATED_AT = "invoice_generated_at";
    const NOTES = "notes";
    const INTERNAL_NOTES = "internal_notes";
    const ORDER_DATE = "order_date";
    const PROCESSED_AT = "processed_at";
    const COMPLETED_AT = "completed_at";
    const CANCELLED_AT = "cancelled_at";
    const CANCELLATION_REASON = "cancellation_reason";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::ORDER_NUMBER,
        self::CLIENT_ORDER_NUMBER,
        self::CLIENT_ID,
        self::BILLING_CONFIG_ID,
        self::INVOICE_ID,
        self::BILLING_SYNC_STATUS,
        self::BILLING_SYNC_MESSAGE,
        self::PACKAGE_ID,
        self::ORDER_TYPE,
        self::SUBTOTAL,
        self::DISCOUNT_AMOUNT,
        self::TAX_AMOUNT,
        self::TAX_PERCENTAGE,
        self::TOTAL_AMOUNT,
        self::PAYMENT_STATUS,
        self::PAYMENT_METHOD,
        self::PAYMENT_REFERENCE,
        self::PAYMENT_DUE_DATE,
        self::INVOICE_NUMBER,
        self::INVOICE_GENERATED_AT,
        self::NOTES,
        self::INTERNAL_NOTES,
        self::ORDER_DATE,
        self::PROCESSED_AT,
        self::COMPLETED_AT,
        self::CANCELLED_AT,
        self::CANCELLATION_REASON,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];

    public function orderCandidates(): HasMany
    {
        return $this->hasMany(OrderCandidate::class, OrderCandidate::ORDER_ID);
    }

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(
            Candidate::class,
            "order_candidates",
            OrderCandidate::ORDER_ID,
            OrderCandidate::CANDIDATE_ID
        );
    }
}
