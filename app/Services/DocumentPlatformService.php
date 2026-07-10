<?php

namespace App\Services;

use App\Repositories\DocumentPlatformRepository;

/**
 * @property DocumentPlatformRepository $repository
 */
class DocumentPlatformService extends BaseService
{
    
    public function __construct(DocumentPlatformRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function platformName()
    {
        return $this->repository->platformName();
    }

    public function platformCode()
    {
        return $this->repository->platformCode();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }
    // functions
}