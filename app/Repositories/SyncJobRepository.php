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
        return SyncJob::JOB_TYPE;
    }

    public function clientId()
    {
        return SyncJob::CLIENT_ID;
    }

    public function configId()
    {
        return SyncJob::CONFIG_ID;
    }

    public function status()
    {
        return SyncJob::STATUS;
    }

    public function itemsProcessed()
    {
        return SyncJob::ITEMS_PROCESSED;
    }

    public function itemsFailed()
    {
        return SyncJob::ITEMS_FAILED;
    }

    public function startedAt()
    {
        return SyncJob::STARTED_AT;
    }

    public function completedAt()
    {
        return SyncJob::COMPLETED_AT;
    }

    public function errorMessage()
    {
        return SyncJob::ERROR_MESSAGE;
    }

    public function syncLog()
    {
        return SyncJob::SYNC_LOG;
    }
    // functions
}