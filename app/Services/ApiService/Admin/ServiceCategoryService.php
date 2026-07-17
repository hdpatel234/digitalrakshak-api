<?php

namespace App\Services\ApiService\Admin;

use App\Models\ServiceCategory;

class ServiceCategoryService
{
    public function getServiceCategories()
    {
        return ServiceCategory::select('id', 'category_name', 'category_code')->get();
    }
}
