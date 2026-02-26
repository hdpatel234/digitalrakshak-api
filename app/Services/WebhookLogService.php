<?php

namespace App\Services;

use App\Repositories\WebhookLogRepository;

class WebhookLogService extends BaseService
{
    protected $repository;
    
    public function __construct(WebhookLogRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function source()
    {
        return $this->repository->source();
    }

    public function platform()
    {
        return $this->repository->platform();
    }

    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function eventType()
    {
        return $this->repository->eventType();
    }

    public function payload()
    {
        return $this->repository->payload();
    }

    public function headers()
    {
        return $this->repository->headers();
    }

    public function processed()
    {
        return $this->repository->processed();
    }

    public function processedAt()
    {
        return $this->repository->processedAt();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }
    // functions
}