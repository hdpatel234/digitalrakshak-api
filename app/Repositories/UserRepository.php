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
        return User::CLIENT_ID;
    }
    public function userType()
    {
        return User::USER_TYPE;
    }
    public function firstName()
    {
        return User::FIRST_NAME;
    }
    public function lastName()
    {
        return User::LAST_NAME;
    }
    public function email()
    {
        return User::EMAIL;
    }
    public function emailVerifiedAt()
    {
        return User::EMAIL_VERIFIED_AT;
    }
    public function phoneCode()
    {
        return User::PHONE_CODE;
    }
    public function phone()
    {
        return User::PHONE;
    }
    public function password()
    {
        return User::PASSWORD;
    }
    public function rememberToken()
    {
        return User::REMEMBER_TOKEN;
    }
    public function avatar()
    {
        return User::AVATAR;
    }
    public function lastLoginAt()
    {
        return User::LAST_LOGIN_AT;
    }
    public function lastLoginIp()
    {
        return User::LAST_LOGIN_IP;
    }
    public function lastLoginBrowser()
    {
        return User::LAST_LOGIN_BROWSER;
    }
    public function lastLoginDevice()
    {
        return User::LAST_LOGIN_DEVICE;
    }

    public function lastLoginOs()
    {
        return User::LAST_LOGIN_OS;
    }

    public function lastLoginProvider()
    {
        return User::LAST_LOGIN_PROVIDER;
    }

    public function lastLoginProviderId()
    {
        return User::LAST_LOGIN_PROVIDER_ID;
    }

    public function status()
    {
        return User::STATUS;
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
