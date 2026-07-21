<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class VerificationReport extends BaseModel
{
    use SoftDeletes;
    protected $table = "verification_reports";

    const CANDIDATE_SERVICE_ID = "candidate_service_id";
    const REPORT_TYPE = "report_type";
    const REPORT_DATA = "report_data";
    const SUMMARY = "summary";
    const OVERALL_STATUS = "overall_status";
    const CONFIDENCE_SCORE = "confidence_score";
    const REPORT_PATH = "report_path";
    const GENERATED_BY = "generated_by";
    const GENERATED_AT = "generated_at";
    const DOWNLOADED_COUNT = "downloaded_count";
    const LAST_DOWNLOADED_AT = "last_downloaded_at";
    const CREATED_BY = "created_by";
    const UPDATED_BY = "updated_by";
    const DELETED_BY = "deleted_by";
    protected $fillable = [
        self::CANDIDATE_SERVICE_ID,
        self::REPORT_TYPE,
        self::REPORT_DATA,
        self::SUMMARY,
        self::OVERALL_STATUS,
        self::CONFIDENCE_SCORE,
        self::REPORT_PATH,
        self::GENERATED_BY,
        self::GENERATED_AT,
        self::DOWNLOADED_COUNT,
        self::LAST_DOWNLOADED_AT,
        self::CREATED_BY,
        self::UPDATED_BY,
        self::DELETED_BY,
    ];
}
