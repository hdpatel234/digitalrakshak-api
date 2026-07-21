<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProcessingQueue extends BaseModel
{
    use SoftDeletes;

    
    protected $table = "service_processing_queue";
    
    const ORDER_ITEM_ID = "order_item_id";
    const SERVICE_ID = "service_id";
    const CANDIDATE_ID = "candidate_id";
    const PROCESSING_RULE_ID = "processing_rule_id";
    const STATUS = "status";
    const ATTEMPTS = "attempts";
    const MAX_ATTEMPTS = "max_attempts";
    const REQUEST_DATA = "request_data";
    const RESPONSE_DATA = "response_data";
    const ERROR_MESSAGE = "error_message";
    const PROCESSED_AT = "processed_at";
    const NEXT_RETRY_AT = "next_retry_at";
    const COMPLETED_AT = "completed_at";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    protected $fillable = [
        self::ORDER_ITEM_ID,
        self::SERVICE_ID,
        self::CANDIDATE_ID,
        self::PROCESSING_RULE_ID,
        self::STATUS,
        self::ATTEMPTS,
        self::MAX_ATTEMPTS,
        self::REQUEST_DATA,
        self::RESPONSE_DATA,
        self::ERROR_MESSAGE,
        self::PROCESSED_AT,
        self::NEXT_RETRY_AT,
        self::COMPLETED_AT,
        self::CREATED_BY,
        self::UPDATED_BY,
    ];
}
