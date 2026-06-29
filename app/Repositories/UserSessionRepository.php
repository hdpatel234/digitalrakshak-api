<?php

namespace App\Repositories;

use App\Models\UserSession;

class UserSessionRepository extends BaseRepository
{
    public function __construct(UserSession $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return UserSession::USER_ID;
    }

    public function accessTokenId()
    {
        return UserSession::ACCESS_TOKEN_ID;
    }

    public function ipAddress()
    {
        return UserSession::IP_ADDRESS;
    }

    public function userAgent()
    {
        return UserSession::USER_AGENT;
    }

    public function browser()
    {
        return UserSession::BROWSER;
    }

    public function os()
    {
        return UserSession::OS;
    }

    public function device()
    {
        return UserSession::DEVICE;
    }

    public function isActive()
    {
        return UserSession::IS_ACTIVE;
    }
    // functions
    public function markInactive($tokenId)
    {
        return UserSession::where(UserSession::ACCESS_TOKEN_ID, $tokenId)->update([UserSession::IS_ACTIVE => false]);
    }
}