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

    public function serverId()
    {
        return EmailLog::SERVER_ID;
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

    public function metadata()
    {
        return EmailLog::METADATA;
    }
    // functions
}
