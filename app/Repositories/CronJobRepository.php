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

    // functions
    public function getAllOrderedDesc()
    {
        return $this->query()->orderBy($this->id(), 'desc')->get();
    }
}
