<?php

namespace App\Services;

use App\Repositories\VerificationWorkflowConfigRepository;

/**
 * @property VerificationWorkflowConfigRepository $repository
 */
class VerificationWorkflowConfigService extends BaseService
{
    public function __construct(protected VerificationWorkflowConfigRepository $repository) {}

    // column constants
    public function serviceId()
    {
        return $this->repository->serviceId();
    }

    public function workflowName()
    {
        return $this->repository->workflowName();
    }

    public function workflowSteps()
    {
        return $this->repository->workflowSteps();
    }

    public function autoStart()
    {
        return $this->repository->autoStart();
    }

    public function timeoutMinutes()
    {
        return $this->repository->timeoutMinutes();
    }

    public function escalationAfterMinutes()
    {
        return $this->repository->escalationAfterMinutes();
    }

    public function escalationUserId()
    {
        return $this->repository->escalationUserId();
    }

    public function notificationConfig()
    {
        return $this->repository->notificationConfig();
    }
    // functions
}