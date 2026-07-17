<?php

namespace App\Http\Controllers\Api\Admin;

use App\Services\ApiService\Admin\ServiceCategoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ServiceCategoryController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected ServiceCategoryService $serviceCategoryService
    ) {}

    /**
     * Display a listing of the service categories.
     */
    public function index(): JsonResponse
    {
        addInfoLog("Admin service category list request");

        $categories = $this->serviceCategoryService->getServiceCategories();

        return $this->success('Service categories retrieved successfully.', $categories);
    }
}
