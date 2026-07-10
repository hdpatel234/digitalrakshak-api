<?php

namespace App\Services;

use App\Repositories\UserSavedSearchRepository;

/**
 * @property UserSavedSearchRepository $repository
 */
class UserSavedSearchService extends BaseService
{
    
    public function __construct(UserSavedSearchRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function userId()
    {
        return $this->repository->userId();
    }

    public function searchName()
    {
        return $this->repository->searchName();
    }

    public function entityType()
    {
        return $this->repository->entityType();
    }

    public function filters()
    {
        return $this->repository->filters();
    }

    public function columns()
    {
        return $this->repository->columns();
    }

    public function sort()
    {
        return $this->repository->sort();
    }

    public function isShared()
    {
        return $this->repository->isShared();
    }
    // functions
}