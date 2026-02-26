<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCandidate extends BaseModel
{
    
    protected $table = "order_candidates";
    
    const ORDER_ID = "order_id";
    const CANDIDATE_ID = "candidate_id";
    const CANDIDATE_DATA = "candidate_data";
    const SUBTOTAL = "subtotal";
    const DISCOUNT_AMOUNT = "discount_amount";
    const TAX_AMOUNT = "tax_amount";
    const TOTAL_AMOUNT = "total_amount";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::ORDER_ID,
        self::CANDIDATE_ID,
        self::CANDIDATE_DATA,
        self::SUBTOTAL,
        self::DISCOUNT_AMOUNT,
        self::TAX_AMOUNT,
        self::TOTAL_AMOUNT,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}