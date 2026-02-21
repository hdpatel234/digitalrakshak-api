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
    public function password()
    {
        return $this->model::PASSWORD;
    }
    public function rememberToken()
    {
        return $this->model::REMEMBER_TOKEN;
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
        return $this->query()
            ->where($this->email(), $email)
            ->first();
    }
}
