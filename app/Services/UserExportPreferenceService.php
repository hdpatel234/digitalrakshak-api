<?php

namespace App\Services;

use App\Repositories\UserExportPreferenceRepository;

/**
 * @property UserExportPreferenceRepository $repository
 */
class UserExportPreferenceService extends BaseService
{
    
    public function __construct(UserExportPreferenceRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function userId()
    {
        return $this->repository->userId();
    }

    public function defaultFormat()
    {
        return $this->repository->defaultFormat();
    }

    public function paperSize()
    {
        return $this->repository->paperSize();
    }

    public function orientation()
    {
        return $this->repository->orientation();
    }

    public function includeTimestamps()
    {
        return $this->repository->includeTimestamps();
    }

    public function includeMetadata()
    {
        return $this->repository->includeMetadata();
    }

    public function compression()
    {
        return $this->repository->compression();
    }

    public function emailOnComplete()
    {
        return $this->repository->emailOnComplete();
    }
    // functions
}