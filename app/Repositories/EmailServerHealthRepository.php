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
        return $this->model::SERVER_ID;
    }

    public function checkType()
    {
        return $this->model::CHECK_TYPE;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function responseTimeMs()
    {
        return $this->model::RESPONSE_TIME_MS;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }

    public function checkedAt()
    {
        return $this->model::CHECKED_AT;
    }
    // functions
}