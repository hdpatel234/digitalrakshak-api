<?php

namespace App\Services;

use App\Repositories\SocialLoginProviderRepository;

/**
 * @property SocialLoginProviderRepository $repository
 */
class SocialLoginProviderService extends BaseService
{
    
    public function __construct(SocialLoginProviderRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function providerName()
    {
        return $this->repository->providerName();
    }

    public function providerCode()
    {
        return $this->repository->providerCode();
    }

    public function icon()
    {
        return $this->repository->icon();
    }

    public function color()
    {
        return $this->repository->color();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function clientId()
    {
        return $this->repository->clientId();
    }

    public function clientSecret()
    {
        return $this->repository->clientSecret();
    }

    public function redirectUrl()
    {
        return $this->repository->redirectUrl();
    }

    public function scopes()
    {
        return $this->repository->scopes();
    }

    public function authParameters()
    {
        return $this->repository->authParameters();
    }

    public function buttonText()
    {
        return $this->repository->buttonText();
    }

    public function buttonIcon()
    {
        return $this->repository->buttonIcon();
    }

    public function buttonColor()
    {
        return $this->repository->buttonColor();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
    }

    public function isEnabled()
    {
        return $this->repository->isEnabled();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function totalUsers()
    {
        return $this->repository->totalUsers();
    }

    public function totalConnections()
    {
        return $this->repository->totalConnections();
    }

    public function lastUsedAt()
    {
        return $this->repository->lastUsedAt();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }
    // functions
}