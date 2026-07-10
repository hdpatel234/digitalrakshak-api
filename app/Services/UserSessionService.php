<?php

namespace App\Services;

use App\Repositories\UserSessionRepository;

/**
 * @property UserSessionRepository $repository
 */
class UserSessionService extends BaseService
{
    
    public function __construct(UserSessionRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function userId()
    {
        return $this->repository->userId();
    }

    public function accessTokenId()
    {
        return $this->repository->accessTokenId();
    }

    public function ipAddress()
    {
        return $this->repository->ipAddress();
    }

    public function userAgent()
    {
        return $this->repository->userAgent();
    }

    public function browser()
    {
        return $this->repository->browser();
    }

    public function os()
    {
        return $this->repository->os();
    }

    public function device()
    {
        return $this->repository->device();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }
    // functions
}