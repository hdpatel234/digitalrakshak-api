<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ApiService\Admin\DashboardService;
use App\Traits\ApiResponse;

class DashboardController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function overview(Request $request): JsonResponse
    {
        addInfoLog("Admin dashboard overview request");

        $data = $this->dashboardService->getOverview();

        return $this->success('Dashboard data fetched successfully.', $data);
    }
}
