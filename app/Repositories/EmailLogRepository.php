<?php

namespace App\Repositories;

use App\Models\EmailLog;

class EmailLogRepository extends BaseRepository
{
    public function __construct(EmailLog $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function emailQueueId()
    {
        return EmailLog::EMAIL_QUEUE_ID;
    }

    public function emailUid()
    {
        return EmailLog::EMAIL_UID;
    }

    public function toEmail()
    {
        return EmailLog::TO_EMAIL;
    }

    public function subject()
    {
        return EmailLog::SUBJECT;
    }

    public function serverId()
    {
        return EmailLog::SERVER_ID;
    }

    public function messageId()
    {
        return EmailLog::MESSAGE_ID;
    }

    public function status()
    {
        return EmailLog::STATUS;
    }

    public function providerResponse()
    {
        return EmailLog::PROVIDER_RESPONSE;
    }

    public function errorMessage()
    {
        return EmailLog::ERROR_MESSAGE;
    }

    public function opens()
    {
        return EmailLog::OPENS;
    }

    public function clicks()
    {
        return EmailLog::CLICKS;
    }

    public function sentAt()
    {
        return EmailLog::SENT_AT;
    }

    public function openedAt()
    {
        return EmailLog::OPENED_AT;
    }

    public function clickedAt()
    {
        return EmailLog::CLICKED_AT;
    }

    public function metadata()
    {
        return EmailLog::METADATA;
    }
    // functions
}