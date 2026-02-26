<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateService extends BaseModel
{
    
    protected $table = "candidate_services";
    
    const CANDIDATE_ID = "candidate_id";
    const SERVICE_ID = "service_id";
    const ORDER_ID = "order_id";
    const ORDER_ITEM_ID = "order_item_id";
    const PRICE_PAID = "price_paid";
    const PROCESSING_RULE_ID = "processing_rule_id";
    const PROCESSING_STATUS = "processing_status";
    const PROCESSING_ATTEMPTS = "processing_attempts";
    const PROCESSED_AT = "processed_at";
    const COMPLETED_AT = "completed_at";
    const ERROR_MESSAGE = "error_message";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::CANDIDATE_ID,
        self::SERVICE_ID,
        self::ORDER_ID,
        self::ORDER_ITEM_ID,
        self::PRICE_PAID,
        self::PROCESSING_RULE_ID,
        self::PROCESSING_STATUS,
        self::PROCESSING_ATTEMPTS,
        self::PROCESSED_AT,
        self::COMPLETED_AT,
        self::ERROR_MESSAGE,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}