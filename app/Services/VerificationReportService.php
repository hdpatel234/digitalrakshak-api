<?php

namespace App\Services;

use App\Repositories\VerificationReportRepository;

/**
 * @property VerificationReportRepository $repository
 */
class VerificationReportService extends BaseService
{
    public function __construct(protected VerificationReportRepository $repository) {}

    // column constants
    public function candidateServiceId()
    {
        return $this->repository->candidateServiceId();
    }

    public function reportType()
    {
        return $this->repository->reportType();
    }

    public function reportData()
    {
        return $this->repository->reportData();
    }

    public function summary()
    {
        return $this->repository->summary();
    }

    public function overallStatus()
    {
        return $this->repository->overallStatus();
    }

    public function confidenceScore()
    {
        return $this->repository->confidenceScore();
    }

    public function reportPath()
    {
        return $this->repository->reportPath();
    }

    public function generatedBy()
    {
        return $this->repository->generatedBy();
    }

    public function generatedAt()
    {
        return $this->repository->generatedAt();
    }

    public function downloadedCount()
    {
        return $this->repository->downloadedCount();
    }

    public function lastDownloadedAt()
    {
        return $this->repository->lastDownloadedAt();
    }

    // functions
}
