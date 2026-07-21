<?php

namespace App\Services;

use App\Repositories\UserSocialConnectionRepository;

/**
 * @property UserSocialConnectionRepository $repository
 */
class UserSocialConnectionService extends BaseService
{
    
    public function __construct(UserSocialConnectionRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function userId()
    {
        return $this->repository->userId();
    }

    public function providerId()
    {
        return $this->repository->providerId();
    }

    public function providerUserId()
    {
        return $this->repository->providerUserId();
    }

    public function providerEmail()
    {
        return $this->repository->providerEmail();
    }

    public function providerName()
    {
        return $this->repository->providerName();
    }

    public function providerAvatar()
    {
        return $this->repository->providerAvatar();
    }

    public function accessToken()
    {
        return $this->repository->accessToken();
    }

    public function refreshToken()
    {
        return $this->repository->refreshToken();
    }

    public function tokenExpiresAt()
    {
        return $this->repository->tokenExpiresAt();
    }

    public function scopes()
    {
        return $this->repository->scopes();
    }

    public function rawData()
    {
        return $this->repository->rawData();
    }

    public function lastLoginAt()
    {
        return $this->repository->lastLoginAt();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }
    // functions
}
