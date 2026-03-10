<?php

namespace App\Repositories;

use App\Models\ServiceCategory;

class ServiceCategoryRepository extends BaseRepository
{
    public function __construct(ServiceCategory $model)
    {
        parent::__construct($model);
    }

    // column constants
    public function categoryName()
    {
        return $this->model::CATEGORY_NAME;
    }

    public function categoryCode()
    {
        return $this->model::CATEGORY_CODE;
    }

    public function categorySlug()
    {
        return $this->model::CATEGORY_SLUG;
    }

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function status()
    {
        return $this->model::STATUS;
    }

    public function createdBy()
    {
        return $this->model::CREATED_BY;
    }

    public function updatedBy()
    {
        return $this->model::UPDATED_BY;
    }

    public function deletedBy()
    {
        return $this->model::DELETED_BY;
    }
    // functions
}