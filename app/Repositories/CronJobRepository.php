<?php

namespace App\Repositories;

use App\Models\CronJob;

class CronJobRepository extends BaseRepository
{
    public function __construct(CronJob $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function command()
    {
        return CronJob::COMMAND;
    }

    public function isActive()
    {
        return CronJob::IS_ACTIVE;
    }

    public function lastRunAt()
    {
        return CronJob::LAST_RUN_AT;
    }

    public function status()
    {
        return CronJob::STATUS;
    }

    public function errorMessage()
    {
        return CronJob::ERROR_MESSAGE;
    }

    public function jobKey()
    {
        return CronJob::JOB_KEY;
    }
    public function jobName()
    {
        return CronJob::JOB_NAME;
    }
    public function nextRunAt()
    {
        return CronJob::NEXT_RUN_AT;
    }
    public function priority()
    {
        return CronJob::PRIORITY;
    }
    public function lastRunLogId()
    {
        return CronJob::LAST_RUN_LOG_ID;
    }
    public function jobClass()
    {
        return CronJob::JOB_CLASS;
    }
    public function jobMethod()
    {
        return CronJob::JOB_METHOD;
    }
    public function parameters()
    {
        return CronJob::PARAMETERS;
    }
    public function lastRunStatus()
    {
        return CronJob::LAST_RUN_STATUS;
    }
    public function maxRetries()
    {
        return CronJob::MAX_RETRIES;
    }
    public function retryDelayMinutes()
    {
        return CronJob::RETRY_DELAY_MINUTES;
    }

    // functions
    public function getAllOrderedDesc()
    {
        return $this->query()->orderBy($this->id(), 'desc')->get();
    }
}
