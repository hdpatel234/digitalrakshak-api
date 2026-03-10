<?php

namespace App\Services;

use App\Repositories\ServiceCategoryRepository;

class ServiceCategoryService extends BaseService
{
    protected $repository;
    
    public function __construct(ServiceCategoryRepository $repository)
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

    public function categorySlug()
    {
        return $this->repository->categorySlug();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function status()
    {
        return $this->repository->status();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }

    public function deletedBy()
    {
        return $this->repository->deletedBy();
    }
    // functions
}