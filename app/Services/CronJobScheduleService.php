<?php

namespace App\Services;

use App\Repositories\CronJobScheduleRepository;

/**
 * @property CronJobScheduleRepository $repository
 */
class CronJobScheduleService extends BaseService
{
    public function __construct(protected CronJobScheduleRepository $repository) {}

    // column constants
    public function jobName()
    {
        return $this->repository->jobName();
    }

    public function jobType()
    {
        return $this->repository->jobType();
    }

    public function scheduleType()
    {
        return $this->repository->scheduleType();
    }

    public function cronExpression()
    {
        return $this->repository->cronExpression();
    }

    public function intervalSeconds()
    {
        return $this->repository->intervalSeconds();
    }

    public function timeOfDay()
    {
        return $this->repository->timeOfDay();
    }

    public function dayOfWeek()
    {
        return $this->repository->dayOfWeek();
    }

    public function dayOfMonth()
    {
        return $this->repository->dayOfMonth();
    }

    public function month()
    {
        return $this->repository->month();
    }

    public function timezone()
    {
        return $this->repository->timezone();
    }

    public function maxRuntimeSeconds()
    {
        return $this->repository->maxRuntimeSeconds();
    }

    public function maxRetries()
    {
        return $this->repository->maxRetries();
    }

    public function retryDelaySeconds()
    {
        return $this->repository->retryDelaySeconds();
    }

    public function concurrentInstances()
    {
        return $this->repository->concurrentInstances();
    }

    public function lastRunAt()
    {
        return $this->repository->lastRunAt();
    }

    public function nextRunAt()
    {
        return $this->repository->nextRunAt();
    }

    public function lastRunStatus()
    {
        return $this->repository->lastRunStatus();
    }

    public function lastRunLogId()
    {
        return $this->repository->lastRunLogId();
    }

    // functions
}
