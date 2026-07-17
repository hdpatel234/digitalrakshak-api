<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\ApiService\Client\DashboardService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    use ApiResponse;

    public function __construct(protected DashboardService $dashboardService) {}

    public function index(Request $request)
    {
        addInfoLog("Client dashboard stats request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $data = $this->dashboardService->getDashboardData($clientId);

        return $this->success('Dashboard data fetched successfully', $data);
    }
}
