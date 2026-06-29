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
        return SocialLoginAttempt::PROVIDER_ID;
    }

    public function email()
    {
        return SocialLoginAttempt::EMAIL;
    }

    public function ipAddress()
    {
        return SocialLoginAttempt::IP_ADDRESS;
    }

    public function userAgent()
    {
        return SocialLoginAttempt::USER_AGENT;
    }

    public function status()
    {
        return SocialLoginAttempt::STATUS;
    }

    public function errorMessage()
    {
        return SocialLoginAttempt::ERROR_MESSAGE;
    }
    // functions
}