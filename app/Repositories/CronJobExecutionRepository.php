<?php

namespace App\Repositories;

use App\Models\CronJobExecution;

class CronJobExecutionRepository extends BaseRepository
{
    public function __construct(CronJobExecution $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function jobKey()
    {
        return CronJobExecution::JOB_KEY;
    }
    public function startedAt()
    {
        return CronJobExecution::STARTED_AT;
    }
    public function completedAt()
    {
        return CronJobExecution::COMPLETED_AT;
    }
    public function status()
    {
        return CronJobExecution::STATUS;
    }
    public function triggeredBy()
    {
        return CronJobExecution::TRIGGERED_BY;
    }
    public function triggeredByUserId()
    {
        return CronJobExecution::TRIGGERED_BY_USER_ID;
    }
    public function ipAddress()
    {
        return CronJobExecution::IP_ADDRESS;
    }
    public function durationSeconds()
    {
        return CronJobExecution::DURATION_SECONDS;
    }
    public function output()
    {
        return CronJobExecution::OUTPUT;
    }
    public function errorMessage()
    {
        return CronJobExecution::ERROR_MESSAGE;
    }
    public function errorStack()
    {
        return CronJobExecution::ERROR_STACK;
    }
    public function processedCount()
    {
        return CronJobExecution::PROCESSED_COUNT;
    }
    public function successCount()
    {
        return CronJobExecution::SUCCESS_COUNT;
    }
    public function failedCount()
    {
        return CronJobExecution::FAILED_COUNT;
    }
    public function processedLogs()
    {
        return CronJobExecution::PROCESSED_LOGS;
    }

    // functions
}
