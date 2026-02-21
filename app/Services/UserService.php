<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Services\BaseService;

class UserService extends BaseService
{
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }

    // column costants
    public function firstName()
    {
        return $this->repository->firstName();
    }
    public function lastName()
    {
        return $this->repository->lastName();
    }
    public function email()
    {
        return $this->repository->email();
    }
    public function emailVerifiedAt()
    {
        return $this->repository->emailVerifiedAt();
    }
    public function password()
    {
        return $this->repository->password();
    }
    public function rememberToken()
    {
        return $this->repository->rememberToken();
    }
    public function lastLoginAt()
    {
        return $this->repository->lastLoginAt();
    }
    public function lastLoginIp()
    {
        return $this->repository->lastLoginIp();
    }
    public function lastLoginBrowser()
    {
        return $this->repository->lastLoginBrowser();
    }
    public function lastLoginDevice()
    {
        return $this->repository->lastLoginDevice();
    }
    public function isActive()
    {
        return $this->repository->isActive();
    }
    public function isAdmin()
    {
        return $this->repository->isAdmin();
    }

    // functions
    public function getByEmail($email)
    {
        return $this->repository->getByEmail($email);
    }
}