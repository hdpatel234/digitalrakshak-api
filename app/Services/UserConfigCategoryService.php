<?php

namespace App\Services;

use App\Repositories\UserConfigCategoryRepository;

/**
 * @property UserConfigCategoryRepository $repository
 */
class UserConfigCategoryService extends BaseService
{
    
    public function __construct(UserConfigCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    // column constants
    public function categoryName()
    {
        return $this->repository->categoryName();
    }

    public function categoryCode()
    {
        return $this->repository->categoryCode();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function displayOrder()
    {
        return $this->repository->displayOrder();
    }

    public function icon()
    {
        return $this->repository->icon();
    }

    public function isSystem()
    {
        return $this->repository->isSystem();
    }

    public function isActive()
    {
        return $this->repository->isActive();
    }
    // functions
}