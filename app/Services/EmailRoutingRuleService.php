<?php

namespace App\Services;

use App\Repositories\EmailRoutingRuleRepository;

/**
 * @property EmailRoutingRuleRepository $repository
 */
class EmailRoutingRuleService extends BaseService
{
    
    public function __construct(EmailRoutingRuleRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function ruleName()
    {
        return $this->repository->ruleName();
    }

    public function rulePriority()
    {
        return $this->repository->rulePriority();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function matchType()
    {
        return $this->repository->matchType();
    }

    public function matchValue()
    {
        return $this->repository->matchValue();
    }

    public function matchPattern()
    {
        return $this->repository->matchPattern();
    }

    public function emailType()
    {
        return $this->repository->emailType();
    }

    public function actionType()
    {
        return $this->repository->actionType();
    }

    public function serverId()
    {
        return $this->repository->serverId();
    }

    public function serverGroup()
    {
        return $this->repository->serverGroup();
    }

    public function failoverServerId()
    {
        return $this->repository->failoverServerId();
    }

    public function maxRetries()
    {
        return $this->repository->maxRetries();
    }

    public function retryDelaySeconds()
    {
        return $this->repository->retryDelaySeconds();
    }

    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function timeStart()
    {
        return $this->repository->timeStart();
    }

    public function timeEnd()
    {
        return $this->repository->timeEnd();
    }

    public function daysOfWeek()
    {
        return $this->repository->daysOfWeek();
    }

    public function timesUsed()
    {
        return $this->repository->timesUsed();
    }

    public function lastUsedAt()
    {
        return $this->repository->lastUsedAt();
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