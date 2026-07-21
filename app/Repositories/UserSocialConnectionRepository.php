<?php

namespace App\Repositories;

use App\Models\UserSocialConnection;

class UserSocialConnectionRepository extends BaseRepository
{
    public function __construct(UserSocialConnection $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function userId()
    {
        return UserSocialConnection::USER_ID;
    }

    public function providerId()
    {
        return UserSocialConnection::PROVIDER_ID;
    }

    public function providerUserId()
    {
        return UserSocialConnection::PROVIDER_USER_ID;
    }

    public function providerEmail()
    {
        return UserSocialConnection::PROVIDER_EMAIL;
    }

    public function providerName()
    {
        return UserSocialConnection::PROVIDER_NAME;
    }

    public function providerAvatar()
    {
        return UserSocialConnection::PROVIDER_AVATAR;
    }

    public function accessToken()
    {
        return UserSocialConnection::ACCESS_TOKEN;
    }

    public function refreshToken()
    {
        return UserSocialConnection::REFRESH_TOKEN;
    }

    public function tokenExpiresAt()
    {
        return UserSocialConnection::TOKEN_EXPIRES_AT;
    }

    public function scopes()
    {
        return UserSocialConnection::SCOPES;
    }

    public function rawData()
    {
        return UserSocialConnection::RAW_DATA;
    }

    public function lastLoginAt()
    {
        return UserSocialConnection::LAST_LOGIN_AT;
    }

    public function isActive()
    {
        return UserSocialConnection::IS_ACTIVE;
    }
    // functions
}
