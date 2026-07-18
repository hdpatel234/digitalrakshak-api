<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateServiceData extends BaseModel
{
    use SoftDeletes;

    protected $table = "candidate_service_data";
    
    const CANDIDATE_SERVICE_ID = "candidate_service_id";
    const FIELD_ID = "field_id";
    const FIELD_VALUE = "field_value";
    const DOCUMENT_ID = "document_id";
    const IS_VERIFIED = "is_verified";
    const VERIFIED_AT = "verified_at";
    const VERIFIED_BY = "verified_by";
    const STATUS = "status";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::CANDIDATE_SERVICE_ID,
        self::FIELD_ID,
        self::FIELD_VALUE,
        self::DOCUMENT_ID,
        self::IS_VERIFIED,
        self::VERIFIED_AT,
        self::VERIFIED_BY,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];

    public function candidateService(): BelongsTo
    {
        return $this->belongsTo(CandidateService::class, self::CANDIDATE_SERVICE_ID);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(ServicesField::class, self::FIELD_ID);
    }
}
