<?php

namespace App\Services;

use App\Repositories\ServiceProviderRepository;

/**
 * @property ServiceProviderRepository $repository
 */
class ServiceProviderService extends BaseService
{
    
    public function __construct(ServiceProviderRepository $repository)
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

    public function providerType()
    {
        return $this->repository->providerType();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function website()
    {
        return $this->repository->website();
    }

    public function supportEmail()
    {
        return $this->repository->supportEmail();
    }

    public function supportPhone()
    {
        return $this->repository->supportPhone();
    }

    public function documentationUrl()
    {
        return $this->repository->documentationUrl();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function isDefault()
    {
        return $this->repository->isDefault();
    }

    public function priority()
    {
        return $this->repository->priority();
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
