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
        return UserConfigCategory::CATEGORY_NAME;
    }

    public function categoryCode()
    {
        return UserConfigCategory::CATEGORY_CODE;
    }

    public function displayOrder()
    {
        return UserConfigCategory::DISPLAY_ORDER;
    }

    public function icon()
    {
        return UserConfigCategory::ICON;
    }

    public function isSystem()
    {
        return UserConfigCategory::IS_SYSTEM;
    }

    // functions
}
