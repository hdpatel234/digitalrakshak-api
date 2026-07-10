<?php

namespace App\Services;

use App\Repositories\UserSearchPreferenceRepository;

/**
 * @property UserSearchPreferenceRepository $repository
 */
class UserSearchPreferenceService extends BaseService
{
    
    public function __construct(UserSearchPreferenceRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function userId()
    {
        return $this->repository->userId();
    }

    public function defaultSearchOperator()
    {
        return $this->repository->defaultSearchOperator();
    }

    public function itemsPerPage()
    {
        return $this->repository->itemsPerPage();
    }

    public function saveRecentSearches()
    {
        return $this->repository->saveRecentSearches();
    }

    public function maxRecentSearches()
    {
        return $this->repository->maxRecentSearches();
    }

    public function saveFilters()
    {
        return $this->repository->saveFilters();
    }

    public function defaultDateRange()
    {
        return $this->repository->defaultDateRange();
    }
    // functions
}