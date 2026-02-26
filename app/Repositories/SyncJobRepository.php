<?php

namespace App\Repositories;

use App\Models\SyncJob;

class SyncJobRepository extends BaseRepository
{
    public function __construct(SyncJob $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function jobType()
    {
        return $this->model::JOB_TYPE;
    }

    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function configId()
    {
        return $this->model::CONFIG_ID;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function itemsProcessed()
    {
        return $this->model::ITEMS_PROCESSED;
    }

    public function itemsFailed()
    {
        return $this->model::ITEMS_FAILED;
    }

    public function startedAt()
    {
        return $this->model::STARTED_AT;
    }

    public function completedAt()
    {
        return $this->model::COMPLETED_AT;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }

    public function syncLog()
    {
        return $this->model::SYNC_LOG;
    }
    // functions
}