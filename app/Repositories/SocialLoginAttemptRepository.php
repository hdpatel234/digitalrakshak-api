<?php

namespace App\Repositories;

use App\Models\SocialLoginAttempt;

class SocialLoginAttemptRepository extends BaseRepository
{
    public function __construct(SocialLoginAttempt $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function providerId()
    {
        return $this->model::PROVIDER_ID;
    }

    public function email()
    {
        return $this->model::EMAIL;
    }

    public function ipAddress()
    {
        return $this->model::IP_ADDRESS;
    }

    public function userAgent()
    {
        return $this->model::USER_AGENT;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function errorMessage()
    {
        return $this->model::ERROR_MESSAGE;
    }
    // functions
}