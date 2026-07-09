<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;

class ServiceCategoryController extends BaseController
{
    /**
     * Display a listing of the service categories.
     */
    public function index(): JsonResponse
    {
        $categories = ServiceCategory::select('id', 'category_name', 'category_code')->get();

        return response()->json([
            'status' => true,
            'message' => 'Service categories retrieved successfully.',
            'data' => $categories
        ]);
    }
}
