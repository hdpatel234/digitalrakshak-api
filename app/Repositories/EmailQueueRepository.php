<?php

namespace App\Repositories;

use App\Models\EmailQueue;

class EmailQueueRepository extends BaseRepository
{
    public function __construct(EmailQueue $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function emailUid()
    {
        return $this->model::EMAIL_UID;
    }

    public function toEmail()
    {
        return $this->model::TO_EMAIL;
    }

    public function toName()
    {
        return $this->model::TO_NAME;
    }

    public function cc()
    {
        return $this->model::CC;
    }

    public function bcc()
    {
        return $this->model::BCC;
    }

    public function replyTo()
    {
        return $this->model::REPLY_TO;
    }

    public function subject()
    {
        return $this->model::SUBJECT;
    }

    public function bodyHtml()
    {
        return $this->model::BODY_HTML;
    }

    public function bodyText()
    {
        return $this->model::BODY_TEXT;
    }

    public function templateId()
    {
        return $this->model::TEMPLATE_ID;
    }

    public function emailType()
    {
        return $this->model::EMAIL_TYPE;
    }

    public function priority()
    {
        return $this->model::PRIORITY;
    }

    public function clientId()
    {
        return $this->model::CLIENT_ID;
    }

    public function candidateId()
    {
        return $this->model::CANDIDATE_ID;
    }

    public function orderId()
    {
        return $this->model::ORDER_ID;
    }

    public function userId()
    {
        return $this->model::USER_ID;
    }

    public function assignedServerId()
    {
        return $this->model::ASSIGNED_SERVER_ID;
    }

    public function routingRuleId()
    {
        return $this->model::ROUTING_RULE_ID;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function attempts()
    {
        return $this->model::ATTEMPTS;
    }

    public function maxAttempts()
    {
        return $this->model::MAX_ATTEMPTS;
    }

    public function lastAttemptAt()
    {
        return $this->model::LAST_ATTEMPT_AT;
    }

    public function sentAt()
    {
        return $this->model::SENT_AT;
    }

    public function messageId()
    {
        return $this->model::MESSAGE_ID;
    }

    public function providerResponse()
    {
        return $this->model::PROVIDER_RESPONSE;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }

    public function opens()
    {
        return $this->model::OPENS;
    }

    public function clicks()
    {
        return $this->model::CLICKS;
    }

    public function lastOpenedAt()
    {
        return $this->model::LAST_OPENED_AT;
    }

    public function scheduledAt()
    {
        return $this->model::SCHEDULED_AT;
    }

    public function expiresAt()
    {
        return $this->model::EXPIRES_AT;
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