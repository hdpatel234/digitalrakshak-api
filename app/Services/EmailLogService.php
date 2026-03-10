<?php

namespace App\Services;

use App\Repositories\EmailLogRepository;

class EmailLogService extends BaseService
{
    
    public function __construct(EmailLogRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function emailQueueId()
    {
        return $this->repository->emailQueueId();
    }

    public function emailUid()
    {
        return $this->repository->emailUid();
    }

    public function toEmail()
    {
        return $this->repository->toEmail();
    }

    public function subject()
    {
        return $this->repository->subject();
    }

    public function serverId()
    {
        return $this->repository->serverId();
    }

    public function messageId()
    {
        return $this->repository->messageId();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function providerResponse()
    {
        return $this->repository->providerResponse();
    }

    public function errorMessage()
    {
        return $this->repository->errorMessage();
    }

    public function opens()
    {
        return $this->repository->opens();
    }

    public function clicks()
    {
        return $this->repository->clicks();
    }

    public function sentAt()
    {
        return $this->repository->sentAt();
    }

    public function openedAt()
    {
        return $this->repository->openedAt();
    }

    public function clickedAt()
    {
        return $this->repository->clickedAt();
    }

    public function metadata()
    {
        return $this->repository->metadata();
    }
    // functions
}