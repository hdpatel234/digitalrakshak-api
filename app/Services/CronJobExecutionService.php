<?php

namespace App\Services;

use App\Repositories\CronJobExecutionRepository;

/**
 * @property CronJobExecutionRepository $repository
 */
class CronJobExecutionService extends BaseService
{
    public function __construct(CronJobExecutionRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function jobKey()
    {
        return $this->repository->jobKey();
    }

    public function startedAt()
    {
        return $this->repository->startedAt();
    }

    public function completedAt()
    {
        return $this->repository->completedAt();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function triggeredBy()
    {
        return $this->repository->triggeredBy();
    }

    public function triggeredByUserId()
    {
        return $this->repository->triggeredByUserId();
    }

    public function ipAddress()
    {
        return $this->repository->ipAddress();
    }

    public function durationSeconds()
    {
        return $this->repository->durationSeconds();
    }

    public function output()
    {
        return $this->repository->output();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }

    public function errorStack()
    {
        return $this->repository->errorStack();
    }

    public function processedCount()
    {
        return $this->repository->processedCount();
    }

    public function successCount()
    {
        return $this->repository->successCount();
    }

    public function failedCount()
    {
        return $this->repository->failedCount();
    }

    public function processedLogs()
    {
        return $this->repository->processedLogs();
    }
    // functions
}
