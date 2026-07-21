<?php

namespace App\Repositories;

use App\Models\VerificationReport;

class VerificationReportRepository extends BaseRepository
{
    public function __construct(VerificationReport $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function candidateServiceId()
    {
        return VerificationReport::CANDIDATE_SERVICE_ID;
    }

    public function reportType()
    {
        return VerificationReport::REPORT_TYPE;
    }

    public function reportData()
    {
        return VerificationReport::REPORT_DATA;
    }

    public function summary()
    {
        return VerificationReport::SUMMARY;
    }

    public function overallStatus()
    {
        return VerificationReport::OVERALL_STATUS;
    }

    public function confidenceScore()
    {
        return VerificationReport::CONFIDENCE_SCORE;
    }

    public function reportPath()
    {
        return VerificationReport::REPORT_PATH;
    }

    public function generatedBy()
    {
        return VerificationReport::GENERATED_BY;
    }

    public function generatedAt()
    {
        return VerificationReport::GENERATED_AT;
    }

    public function downloadedCount()
    {
        return VerificationReport::DOWNLOADED_COUNT;
    }

    public function lastDownloadedAt()
    {
        return VerificationReport::LAST_DOWNLOADED_AT;
    }

    // functions
}
