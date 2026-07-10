<?php

namespace App\Services;

use App\Repositories\AiProviderRepository;

/**
 * @property AiProviderRepository $repository
 */
class AiProviderService extends BaseService
{
    protected $repository;
    
    public function __construct(AiProviderRepository $repository)
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

    public function documentationUrl()
    {
        return $this->repository->documentationUrl();
    }

    public function icon()
    {
        return $this->repository->icon();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
    }
    // functions
}