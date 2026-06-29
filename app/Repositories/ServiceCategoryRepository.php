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
        return ServiceCategory::CATEGORY_NAME;
    }

    public function categoryCode()
    {
        return ServiceCategory::CATEGORY_CODE;
    }

    public function categorySlug()
    {
        return ServiceCategory::CATEGORY_SLUG;
    }

    public function description()
    {
        return ServiceCategory::DESCRIPTION;
    }

    public function status()
    {
        return ServiceCategory::STATUS;
    }

    public function createdBy()
    {
        return ServiceCategory::CREATED_BY;
    }

    public function updatedBy()
    {
        return ServiceCategory::UPDATED_BY;
    }

    public function deletedBy()
    {
        return ServiceCategory::DELETED_BY;
    }
    // functions
}