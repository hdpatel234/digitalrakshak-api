<?php

namespace App\Repositories;

use App\Models\ServiceProcessingRule;

class ServiceProcessingRuleRepository extends BaseRepository
{
    public function __construct(ServiceProcessingRule $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serviceId()
    {
        return ServiceProcessingRule::SERVICE_ID;
    }

    public function processingType()
    {
        return ServiceProcessingRule::PROCESSING_TYPE;
    }

    public function apiEndpoint()
    {
        return ServiceProcessingRule::API_ENDPOINT;
    }

    public function apiMethod()
    {
        return ServiceProcessingRule::API_METHOD;
    }

    public function apiHeaders()
    {
        return ServiceProcessingRule::API_HEADERS;
    }

    public function apiMapping()
    {
        return ServiceProcessingRule::API_MAPPING;
    }

    public function emailTemplateId()
    {
        return ServiceProcessingRule::EMAIL_TEMPLATE_ID;
    }

    public function emailTo()
    {
        return ServiceProcessingRule::EMAIL_TO;
    }

    public function ticketPriority()
    {
        return ServiceProcessingRule::TICKET_PRIORITY;
    }

    public function ticketDepartment()
    {
        return ServiceProcessingRule::TICKET_DEPARTMENT;
    }

    public function cronExpression()
    {
        return ServiceProcessingRule::CRON_EXPRESSION;
    }

    public function webhookUrl()
    {
        return ServiceProcessingRule::WEBHOOK_URL;
    }

    public function webhookSecret()
    {
        return ServiceProcessingRule::WEBHOOK_SECRET;
    }

    public function timeoutSeconds()
    {
        return ServiceProcessingRule::TIMEOUT_SECONDS;
    }

    public function retryCount()
    {
        return ServiceProcessingRule::RETRY_COUNT;
    }

    public function retryDelayMinutes()
    {
        return ServiceProcessingRule::RETRY_DELAY_MINUTES;
    }

    public function successStatus()
    {
        return ServiceProcessingRule::SUCCESS_STATUS;
    }

    public function failureStatus()
    {
        return ServiceProcessingRule::FAILURE_STATUS;
    }

    public function isActive()
    {
        return ServiceProcessingRule::IS_ACTIVE;
    }

    public function createdBy()
    {
        return ServiceProcessingRule::CREATED_BY;
    }

    public function updatedBy()
    {
        return ServiceProcessingRule::UPDATED_BY;
    }
    // functions
}
