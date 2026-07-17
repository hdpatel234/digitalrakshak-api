<?php

namespace App\Http\Controllers\Api\Client\Settings;

use App\Http\Controllers\Api\Client\BaseController;
use App\Services\ApiService\Client\CompanySettingService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompanySettingController extends BaseController
{
    use ApiResponse;

    public function __construct(protected CompanySettingService $companySettingService) {}

    public function index(Request $request)
    {
        addInfoLog("Client company setting index request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $settings = $this->companySettingService->getSettings($clientId);
            return $this->success('Company settings fetched successfully.', $settings);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(Request $request)
    {
        addInfoLog("Client company setting update request");

        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $settings = $this->companySettingService->updateSettings($request->all(), $clientId);
            return $this->success('Company settings updated successfully.', $settings);
        } catch (\Exception $e) {
            Log::error('Company settings update failed', [
                'error' => $e->getMessage(),
            ]);
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
