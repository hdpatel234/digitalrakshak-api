<?php

namespace App\Services;

use App\Repositories\EmailServerHealthRepository;

class EmailServerHealthService extends BaseService
{
    protected $repository;
    
    public function __construct(EmailServerHealthRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function serverId()
    {
        return $this->repository->serverId();
    }

    public function checkType()
    {
        return $this->repository->checkType();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function responseTimeMs()
    {
        return $this->repository->responseTimeMs();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }

    public function checkedAt()
    {
        return $this->repository->checkedAt();
    }
    // functions
}