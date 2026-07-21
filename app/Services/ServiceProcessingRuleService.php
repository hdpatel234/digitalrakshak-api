<?php

namespace App\Services;

use App\Repositories\ServiceProcessingRuleRepository;

/**
 * @property ServiceProcessingRuleRepository $repository
 */
class ServiceProcessingRuleService extends BaseService
{
    
    public function __construct(ServiceProcessingRuleRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function serviceId()
    {
        return $this->repository->serviceId();
    }

    public function processingType()
    {
        return $this->repository->processingType();
    }

    public function apiEndpoint()
    {
        return $this->repository->apiEndpoint();
    }

    public function apiMethod()
    {
        return $this->repository->apiMethod();
    }

    public function apiHeaders()
    {
        return $this->repository->apiHeaders();
    }

    public function apiMapping()
    {
        return $this->repository->apiMapping();
    }

    public function emailTemplateId()
    {
        return $this->repository->emailTemplateId();
    }

    public function emailTo()
    {
        return $this->repository->emailTo();
    }

    public function ticketPriority()
    {
        return $this->repository->ticketPriority();
    }

    public function ticketDepartment()
    {
        return $this->repository->ticketDepartment();
    }

    public function cronExpression()
    {
        return $this->repository->cronExpression();
    }

    public function webhookUrl()
    {
        return $this->repository->webhookUrl();
    }

    public function webhookSecret()
    {
        return $this->repository->webhookSecret();
    }

    public function timeoutSeconds()
    {
        return $this->repository->timeoutSeconds();
    }

    public function retryCount()
    {
        return $this->repository->retryCount();
    }

    public function retryDelayMinutes()
    {
        return $this->repository->retryDelayMinutes();
    }

    public function successStatus()
    {
        return $this->repository->successStatus();
    }

    public function failureStatus()
    {
        return $this->repository->failureStatus();
    }

    public function isActive()
    {
        return $this->repository->isActive();
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
