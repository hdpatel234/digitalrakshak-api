<?php

namespace App\Services;

use App\Repositories\EmailQueueRepository;

class EmailQueueService extends BaseService
{
    protected $repository;
    
    public function __construct(EmailQueueRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function emailUid()
    {
        return $this->repository->emailUid();
    }

    public function toEmail()
    {
        return $this->repository->toEmail();
    }

    public function toName()
    {
        return $this->repository->toName();
    }

    public function cc()
    {
        return $this->repository->cc();
    }

    public function bcc()
    {
        return $this->repository->bcc();
    }

    public function replyTo()
    {
        return $this->repository->replyTo();
    }

    public function subject()
    {
        return $this->repository->subject();
    }

    public function bodyHtml()
    {
        return $this->repository->bodyHtml();
    }

    public function bodyText()
    {
        return $this->repository->bodyText();
    }

    public function templateId()
    {
        return $this->repository->templateId();
    }

    public function emailType()
    {
        return $this->repository->emailType();
    }

    public function priority()
    {
        return $this->repository->priority();
    }

    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function candidateId()
    {
        return $this->repository->candidateId();
    }

    public function orderId()
    {
        return $this->repository->orderId();
    }

    public function userId()
    {
        return $this->repository->userId();
    }

    public function assignedServerId()
    {
        return $this->repository->assignedServerId();
    }

    public function routingRuleId()
    {
        return $this->repository->routingRuleId();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function attempts()
    {
        return $this->repository->attempts();
    }

    public function maxAttempts()
    {
        return $this->repository->maxAttempts();
    }

    public function lastAttemptAt()
    {
        return $this->repository->lastAttemptAt();
    }

    public function sentAt()
    {
        return $this->repository->sentAt();
    }

    public function messageId()
    {
        return $this->repository->messageId();
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

    public function lastOpenedAt()
    {
        return $this->repository->lastOpenedAt();
    }

    public function scheduledAt()
    {
        return $this->repository->scheduledAt();
    }

    public function expiresAt()
    {
        return $this->repository->expiresAt();
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