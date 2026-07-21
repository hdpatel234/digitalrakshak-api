<?php

namespace App\Repositories;

use App\Models\FieldComparisonResult;

class FieldComparisonResultRepository extends BaseRepository
{
    public function __construct(FieldComparisonResult $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function verificationResultId()
    {
        return FieldComparisonResult::VERIFICATION_RESULT_ID;
    }

    public function userValue()
    {
        return FieldComparisonResult::USER_VALUE;
    }

    public function apiValue()
    {
        return FieldComparisonResult::API_VALUE;
    }

    public function comparisonResult()
    {
        return FieldComparisonResult::COMPARISON_RESULT;
    }

    public function confidenceScore()
    {
        return FieldComparisonResult::CONFIDENCE_SCORE;
    }

    public function discrepancyNotes()
    {
        return FieldComparisonResult::DISCREPANCY_NOTES;
    }
    // functions
}
