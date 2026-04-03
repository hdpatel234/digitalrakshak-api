<?php

namespace App\Http\Controllers\Api\Client\Service;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\ApiService\Client\ServiceApiService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ServicesController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected ServiceApiService $serviceApiService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $result = $this->serviceApiService->getClientServices($request->all(), $clientId);

        return $this->success('Services fetched successfully.', $result);
    }
}
