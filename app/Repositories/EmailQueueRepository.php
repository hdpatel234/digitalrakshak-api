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
        return EmailQueue::EMAIL_UID;
    }

    public function toEmail()
    {
        return EmailQueue::TO_EMAIL;
    }

    public function toName()
    {
        return EmailQueue::TO_NAME;
    }

    public function cc()
    {
        return EmailQueue::CC;
    }

    public function bcc()
    {
        return EmailQueue::BCC;
    }

    public function replyTo()
    {
        return EmailQueue::REPLY_TO;
    }

    public function subject()
    {
        return EmailQueue::SUBJECT;
    }

    public function bodyHtml()
    {
        return EmailQueue::BODY_HTML;
    }

    public function bodyText()
    {
        return EmailQueue::BODY_TEXT;
    }

    public function templateId()
    {
        return EmailQueue::TEMPLATE_ID;
    }

    public function emailType()
    {
        return EmailQueue::EMAIL_TYPE;
    }

    public function priority()
    {
        return EmailQueue::PRIORITY;
    }

    public function clientId()
    {
        return EmailQueue::CLIENT_ID;
    }

    public function candidateId()
    {
        return EmailQueue::CANDIDATE_ID;
    }

    public function orderId()
    {
        return EmailQueue::ORDER_ID;
    }

    public function userId()
    {
        return EmailQueue::USER_ID;
    }

    public function assignedServerId()
    {
        return EmailQueue::ASSIGNED_SERVER_ID;
    }

    public function routingRuleId()
    {
        return EmailQueue::ROUTING_RULE_ID;
    }

    public function status()
    {
        return EmailQueue::STATUS;
    }

    public function attempts()
    {
        return EmailQueue::ATTEMPTS;
    }

    public function maxAttempts()
    {
        return EmailQueue::MAX_ATTEMPTS;
    }

    public function lastAttemptAt()
    {
        return EmailQueue::LAST_ATTEMPT_AT;
    }

    public function sentAt()
    {
        return EmailQueue::SENT_AT;
    }

    public function messageId()
    {
        return EmailQueue::MESSAGE_ID;
    }

    public function providerResponse()
    {
        return EmailQueue::PROVIDER_RESPONSE;
    }

    public function errorMessage()
    {
        return EmailQueue::ERROR_MESSAGE;
    }

    public function opens()
    {
        return EmailQueue::OPENS;
    }

    public function clicks()
    {
        return EmailQueue::CLICKS;
    }

    public function lastOpenedAt()
    {
        return EmailQueue::LAST_OPENED_AT;
    }

    public function scheduledAt()
    {
        return EmailQueue::SCHEDULED_AT;
    }

    public function expiresAt()
    {
        return EmailQueue::EXPIRES_AT;
    }

    public function createdBy()
    {
        return EmailQueue::CREATED_BY;
    }

    public function updatedBy()
    {
        return EmailQueue::UPDATED_BY;
    }
    // functions
}