<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateServiceDatum extends BaseModel
{
    
    protected $table = "candidate_service_data";
    
    const CANDIDATE_SERVICE_ID = "candidate_service_id";
    const FIELD_ID = "field_id";
    const FIELD_VALUE = "field_value";
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
        self::IS_VERIFIED,
        self::VERIFIED_AT,
        self::VERIFIED_BY,
        self::STATUS,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}