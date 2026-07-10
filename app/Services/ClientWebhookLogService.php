<?php

namespace App\Services;

use App\Repositories\ClientWebhookLogRepository;

/**
 * @property ClientWebhookLogRepository $repository
 */
class ClientWebhookLogService extends BaseService
{
    
    public function __construct(ClientWebhookLogRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function webhookId()
    {
        return $this->repository->webhookId();
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

    public function responseCode()
    {
        return $this->repository->responseCode();
    }

    public function responseBody()
    {
        return $this->repository->responseBody();
    }

    public function responseTimeMs()
    {
        return $this->repository->responseTimeMs();
    }

    public function attempt()
    {
        return $this->repository->attempt();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }

    public function nextRetryAt()
    {
        return $this->repository->nextRetryAt();
    }
    // functions
}