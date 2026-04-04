<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    // column costants
    public function clientID()
    {
        return $this->model::CLIENT_ID;
    }
    public function userType()
    {
        return $this->model::USER_TYPE;
    }
    public function firstName()
    {
        return $this->model::FIRST_NAME;
    }
    public function lastName()
    {
        return $this->model::LAST_NAME;
    }
    public function email()
    {
        return $this->model::EMAIL;
    }
    public function emailVerifiedAt()
    {
        return $this->model::EMAIL_VERIFIED_AT;
    }
    public function phoneCode()
    {
        return $this->model::PHONE_CODE;
    }
    public function phone()
    {
        return $this->model::PHONE;
    }
    public function password()
    {
        return $this->model::PASSWORD;
    }
    public function rememberToken()
    {
        return $this->model::REMEMBER_TOKEN;
    }
    public function avatar()
    {
        return $this->model::AVATAR;
    }
    public function lastLoginAt()
    {
        return $this->model::LAST_LOGIN_AT;
    }
    public function lastLoginIp()
    {
        return $this->model::LAST_LOGIN_IP;
    }
    public function lastLoginBrowser()
    {
        return $this->model::LAST_LOGIN_BROWSER;
    }
    public function lastLoginDevice()
    {
        return $this->model::LAST_LOGIN_DEVICE;
    }

    public function lastLoginOs()
    {
        return $this->model::LAST_LOGIN_OS;
    }

    public function lastLoginProvider()
    {
        return $this->model::LAST_LOGIN_PROVIDER;
    }

    public function lastLoginProviderId()
    {
        return $this->model::LAST_LOGIN_PROVIDER_ID;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }
    public function isAdmin()
    {
        return $this->model::IS_ADMIN;
    }

    // functions
    public function getByEmail(string $email): ?User
    {
        return $this->query()->where($this->email(), $email)->first();
    }

    public function getUsersByClientId($clientId)
    {
        return $this->query()->where($this->clientID(), $clientId)->get();
    }
}
