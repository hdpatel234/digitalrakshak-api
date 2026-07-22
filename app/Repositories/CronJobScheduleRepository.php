<?php

namespace App\Repositories;

use App\Models\CronJobSchedule;

class CronJobScheduleRepository extends BaseRepository
{
    public function __construct(CronJobSchedule $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function jobName()
    {
        return CronJobSchedule::JOB_NAME;
    }

    public function jobType()
    {
        return CronJobSchedule::JOB_TYPE;
    }

    public function scheduleType()
    {
        return CronJobSchedule::SCHEDULE_TYPE;
    }

    public function cronExpression()
    {
        return CronJobSchedule::CRON_EXPRESSION;
    }

    public function intervalSeconds()
    {
        return CronJobSchedule::INTERVAL_SECONDS;
    }

    public function timeOfDay()
    {
        return CronJobSchedule::TIME_OF_DAY;
    }

    public function dayOfWeek()
    {
        return CronJobSchedule::DAY_OF_WEEK;
    }

    public function dayOfMonth()
    {
        return CronJobSchedule::DAY_OF_MONTH;
    }

    public function month()
    {
        return CronJobSchedule::MONTH;
    }

    public function timezone()
    {
        return CronJobSchedule::TIMEZONE;
    }

    public function maxRuntimeSeconds()
    {
        return CronJobSchedule::MAX_RUNTIME_SECONDS;
    }

    public function maxRetries()
    {
        return CronJobSchedule::MAX_RETRIES;
    }

    public function retryDelaySeconds()
    {
        return CronJobSchedule::RETRY_DELAY_SECONDS;
    }

    public function concurrentInstances()
    {
        return CronJobSchedule::CONCURRENT_INSTANCES;
    }

    public function lastRunAt()
    {
        return CronJobSchedule::LAST_RUN_AT;
    }

    public function nextRunAt()
    {
        return CronJobSchedule::NEXT_RUN_AT;
    }

    public function lastRunStatus()
    {
        return CronJobSchedule::LAST_RUN_STATUS;
    }

    public function lastRunLogId()
    {
        return CronJobSchedule::LAST_RUN_LOG_ID;
    }

    // functions
}
