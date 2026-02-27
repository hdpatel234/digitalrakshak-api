<?php

namespace App\Services;

use App\Repositories\ClientWebhookRepository;

class ClientWebhookService extends BaseService
{
    protected $repository;
    
    public function __construct(ClientWebhookRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function webhookName()
    {
        return $this->repository->webhookName();
    }

    public function webhookUrl()
    {
        return $this->repository->webhookUrl();
    }

    public function webhookSecret()
    {
        return $this->repository->webhookSecret();
    }

    public function events()
    {
        return $this->repository->events();
    }

    public function headers()
    {
        return $this->repository->headers();
    }

    public function format()
    {
        return $this->repository->format();
    }

    public function maxRetries()
    {
        return $this->repository->maxRetries();
    }

    public function retryDelaySeconds()
    {
        return $this->repository->retryDelaySeconds();
    }

    public function timeoutSeconds()
    {
        return $this->repository->timeoutSeconds();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function lastTriggeredAt()
    {
        return $this->repository->lastTriggeredAt();
    }

    public function lastSuccessAt()
    {
        return $this->repository->lastSuccessAt();
    }

    public function lastFailureAt()
    {
        return $this->repository->lastFailureAt();
    }

    public function lastError()
    {
        return $this->repository->lastError();
    }

    public function totalSuccess()
    {
        return $this->repository->totalSuccess();
    }

    public function totalFailures()
    {
        return $this->repository->totalFailures();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }
    // functions
}