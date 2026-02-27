<?php

namespace App\Repositories;

use App\Models\UserConfigCategory;

class UserConfigCategoryRepository extends BaseRepository
{
    public function __construct(UserConfigCategory $model)
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

    public function description()
    {
        return $this->model::DESCRIPTION;
    }

    public function displayOrder()
    {
        return $this->model::DISPLAY_ORDER;
    }

    public function icon()
    {
        return $this->model::ICON;
    }

    public function isSystem()
    {
        return $this->model::IS_SYSTEM;
    }

    public function isActive()
    {
        return $this->model::IS_ACTIVE;
    }
    // functions
}