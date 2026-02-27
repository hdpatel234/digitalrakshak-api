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
        return $this->model::EMAIL_QUEUE_ID;
    }

    public function emailUid()
    {
        return $this->model::EMAIL_UID;
    }

    public function toEmail()
    {
        return $this->model::TO_EMAIL;
    }

    public function subject()
    {
        return $this->model::SUBJECT;
    }

    public function serverId()
    {
        return $this->model::SERVER_ID;
    }

    public function messageId()
    {
        return $this->model::MESSAGE_ID;
    }

    public function status()
    {
        return $this->model::STATUS;
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

    public function sentAt()
    {
        return $this->model::SENT_AT;
    }

    public function openedAt()
    {
        return $this->model::OPENED_AT;
    }

    public function clickedAt()
    {
        return $this->model::CLICKED_AT;
    }

    public function metadata()
    {
        return $this->model::METADATA;
    }
    // functions
}