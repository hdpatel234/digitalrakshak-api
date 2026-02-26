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
        return $this->model::SERVICE_ID;
    }

    public function processingType()
    {
        return $this->model::PROCESSING_TYPE;
    }

    public function apiEndpoint()
    {
        return $this->model::API_ENDPOINT;
    }

    public function apiMethod()
    {
        return $this->model::API_METHOD;
    }

    public function apiHeaders()
    {
        return $this->model::API_HEADERS;
    }

    public function apiMapping()
    {
        return $this->model::API_MAPPING;
    }

    public function emailTemplateId()
    {
        return $this->model::EMAIL_TEMPLATE_ID;
    }

    public function emailTo()
    {
        return $this->model::EMAIL_TO;
    }

    public function ticketPriority()
    {
        return $this->model::TICKET_PRIORITY;
    }

    public function ticketDepartment()
    {
        return $this->model::TICKET_DEPARTMENT;
    }

    public function cronExpression()
    {
        return $this->model::CRON_EXPRESSION;
    }

    public function webhookUrl()
    {
        return $this->model::WEBHOOK_URL;
    }

    public function webhookSecret()
    {
        return $this->model::WEBHOOK_SECRET;
    }

    public function timeoutSeconds()
    {
        return $this->model::TIMEOUT_SECONDS;
    }

    public function retryCount()
    {
        return $this->model::RETRY_COUNT;
    }

    public function retryDelayMinutes()
    {
        return $this->model::RETRY_DELAY_MINUTES;
    }

    public function successStatus()
    {
        return $this->model::SUCCESS_STATUS;
    }

    public function failureStatus()
    {
        return $this->model::FAILURE_STATUS;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }
    // functions
}