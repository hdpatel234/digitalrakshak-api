<?php

namespace App\Repositories;

use App\Models\VerificationWorkflowConfig;

class VerificationWorkflowConfigRepository extends BaseRepository
{
    public function __construct(VerificationWorkflowConfig $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serviceId()
    {
        return VerificationWorkflowConfig::SERVICE_ID;
    }

    public function workflowName()
    {
        return VerificationWorkflowConfig::WORKFLOW_NAME;
    }

    public function workflowSteps()
    {
        return VerificationWorkflowConfig::WORKFLOW_STEPS;
    }

    public function autoStart()
    {
        return VerificationWorkflowConfig::AUTO_START;
    }

    public function timeoutMinutes()
    {
        return VerificationWorkflowConfig::TIMEOUT_MINUTES;
    }

    public function escalationAfterMinutes()
    {
        return VerificationWorkflowConfig::ESCALATION_AFTER_MINUTES;
    }

    public function escalationUserId()
    {
        return VerificationWorkflowConfig::ESCALATION_USER_ID;
    }

    public function notificationConfig()
    {
        return VerificationWorkflowConfig::NOTIFICATION_CONFIG;
    }
    // functions
}