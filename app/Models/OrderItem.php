<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "order_items";
    
    const ORDER_ID = "order_id";
    const ORDER_CANDIDATE_ID = "order_candidate_id";
    const SERVICE_ID = "service_id";
    const SUPPORT_CONFIG_ID = "support_config_id";
    const TICKET_ID = "ticket_id";
    const REPORT_DOCUMENT_ID = "report_document_id";
    const SUPPORT_SYNC_STATUS = "support_sync_status";
    const PROCESSING_RULE_ID = "processing_rule_id";
    const PROCESSING_STATUS = "processing_status";
    const PROCESSING_ATTEMPTS = "processing_attempts";
    const PROCESSED_AT = "processed_at";
    const COMPLETED_AT = "completed_at";
    const ERROR_MESSAGE = "error_message";
    const UNIT_PRICE = "unit_price";
    const QUANTITY = "quantity";
    const DISCOUNT_AMOUNT = "discount_amount";
    const TAX_AMOUNT = "tax_amount";
    const TAX_PERCENTAGE = "tax_percentage";
    const TOTAL_PRICE = "total_price";
    const SERVICE_DATA = "service_data";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::ORDER_ID,
        self::ORDER_CANDIDATE_ID,
        self::SERVICE_ID,
        self::SUPPORT_CONFIG_ID,
        self::TICKET_ID,
        self::REPORT_DOCUMENT_ID,
        self::SUPPORT_SYNC_STATUS,
        self::PROCESSING_RULE_ID,
        self::PROCESSING_STATUS,
        self::PROCESSING_ATTEMPTS,
        self::PROCESSED_AT,
        self::COMPLETED_AT,
        self::ERROR_MESSAGE,
        self::UNIT_PRICE,
        self::QUANTITY,
        self::DISCOUNT_AMOUNT,
        self::TAX_AMOUNT,
        self::TAX_PERCENTAGE,
        self::TOTAL_PRICE,
        self::SERVICE_DATA,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}
