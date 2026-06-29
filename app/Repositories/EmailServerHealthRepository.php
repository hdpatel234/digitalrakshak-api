<?php

namespace App\Repositories;

use App\Models\EmailServerHealth;

class EmailServerHealthRepository extends BaseRepository
{
    public function __construct(EmailServerHealth $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function serverId()
    {
        return EmailServerHealth::SERVER_ID;
    }

    public function checkType()
    {
        return EmailServerHealth::CHECK_TYPE;
    }

    public function status()
    {
        return EmailServerHealth::STATUS;
    }

    public function responseTimeMs()
    {
        return EmailServerHealth::RESPONSE_TIME_MS;
    }

    public function errorMessage()
    {
        return EmailServerHealth::ERROR_MESSAGE;
    }

    public function checkedAt()
    {
        return EmailServerHealth::CHECKED_AT;
    }
    // functions
}