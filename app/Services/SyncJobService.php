<?php

namespace App\Services;

use App\Repositories\SyncJobRepository;

class SyncJobService extends BaseService
{
    
    public function __construct(SyncJobRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function jobType()
    {
        return $this->repository->jobType();
    }

    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function configId()
    {
        return $this->repository->configId();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function itemsProcessed()
    {
        return $this->repository->itemsProcessed();
    }

    public function itemsFailed()
    {
        return $this->repository->itemsFailed();
    }

    public function startedAt()
    {
        return $this->repository->startedAt();
    }

    public function completedAt()
    {
        return $this->repository->completedAt();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }

    public function syncLog()
    {
        return $this->repository->syncLog();
    }
    // functions
}