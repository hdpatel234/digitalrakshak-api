<?php

namespace App\Services\ApiService\Admin;

use App\Repositories\ServiceCategoryRepository;

class ServiceCategoryService
{
    public function __construct(
        protected ServiceCategoryRepository $repo
    ) {}

    public function getServiceCategories()
    {
        return $this->repo->query()->select([$this->repo->id(), $this->repo->categoryName(), $this->repo->categoryCode()])->get();
    }
}
