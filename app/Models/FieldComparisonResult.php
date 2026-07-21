<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class FieldComparisonResult extends BaseModel
{
    use SoftDeletes;
    protected $table = "field_comparison_results";

    const VERIFICATION_RESULT_ID = "verification_result_id";
    const USER_VALUE = "user_value";
    const API_VALUE = "api_value";
    const COMPARISON_RESULT = "comparison_result";
    const CONFIDENCE_SCORE = "confidence_score";
    const DISCREPANCY_NOTES = "discrepancy_notes";
    protected $fillable = [
        self::VERIFICATION_RESULT_ID,
        self::USER_VALUE,
        self::API_VALUE,
        self::COMPARISON_RESULT,
        self::CONFIDENCE_SCORE,
        self::DISCREPANCY_NOTES,
    ];
}
