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
        return $this->model::USER_ID;
    }

    public function providerId()
    {
        return $this->model::PROVIDER_ID;
    }

    public function providerUserId()
    {
        return $this->model::PROVIDER_USER_ID;
    }

    public function providerEmail()
    {
        return $this->model::PROVIDER_EMAIL;
    }

    public function providerName()
    {
        return $this->model::PROVIDER_NAME;
    }

    public function providerAvatar()
    {
        return $this->model::PROVIDER_AVATAR;
    }

    public function accessToken()
    {
        return $this->model::ACCESS_TOKEN;
    }

    public function refreshToken()
    {
        return $this->model::REFRESH_TOKEN;
    }

    public function tokenExpiresAt()
    {
        return $this->model::TOKEN_EXPIRES_AT;
    }

    public function scopes()
    {
        return $this->model::SCOPES;
    }

    public function rawData()
    {
        return $this->model::RAW_DATA;
    }

    public function lastLoginAt()
    {
        return $this->model::LAST_LOGIN_AT;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }
    // functions
}