<?php

namespace App\Services;

use App\Repositories\CronJobRepository;

/**
 * @property CronJobRepository $repository
 */
class CronJobService extends BaseService
{

    public function __construct(CronJobRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function command()
    {
        return $this->repository->command();
    }

    public function lastRunAt()
    {
        return $this->repository->lastRunAt();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }

    public function jobKey()
    {
        return $this->repository->jobKey();
    }
    public function jobName()
    {
        return $this->repository->jobName();
    }
    public function nextRunAt()
    {
        return $this->repository->nextRunAt();
    }
    public function priority()
    {
        return $this->repository->priority();
    }
    public function lastRunLogId()
    {
        return $this->repository->lastRunLogId();
    }
    public function jobClass()
    {
        return $this->repository->jobClass();
    }
    public function jobMethod()
    {
        return $this->repository->jobMethod();
    }
    public function parameters()
    {
        return $this->repository->parameters();
    }
    public function lastRunStatus()
    {
        return $this->repository->lastRunStatus();
    }
    public function maxRetries()
    {
        return $this->repository->maxRetries();
    }
    public function retryDelayMinutes()
    {
        return $this->repository->retryDelayMinutes();
    }

    // functions
}
