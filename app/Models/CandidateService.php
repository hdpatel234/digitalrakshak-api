<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateService extends BaseModel
{
    use SoftDeletes;

    protected $table = "candidate_services";

    const CANDIDATE_ID = "candidate_id";
    const SERVICE_ID = "service_id";
    const ORDER_ID = "order_id";
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

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, self::CANDIDATE_ID);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(CandidateOrder::class, self::ORDER_ID);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, self::SERVICE_ID);
    }

    public function serviceData()
    {
        return $this->hasMany(CandidateServiceData::class, CandidateServiceData::order_item_id);
    }
}
