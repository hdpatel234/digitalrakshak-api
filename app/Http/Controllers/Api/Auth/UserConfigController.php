<?php

namespace App\Http\Controllers\Api\Auth;

use App\Services\UserConfigService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserConfigController extends BaseController
{
    use ApiResponse;
    protected UserConfigService $userConfigService;

    public function __construct(UserConfigService $userConfigService)
    {
        $this->userConfigService = $userConfigService;
    }

    public function index()
    {
        $userId = Auth::guard('api')->id() ?? Auth::id();

        if (!$userId) {
            return $this->error('Unauthenticated.', 401);
        }

        return $this->success('User config fetched successfully', [
            'configs' => $this->userConfigService->getResolvedConfigs($userId),
        ]);
    }

    public function store(Request $request)
    {
        $userId = Auth::guard('api')->id() ?? Auth::id();

        if (!$userId) {
            return $this->error('Unauthenticated.', 401);
        }

        $configs = $request->input('configs', $request->all());
        if (!is_array($configs)) {
            return $this->error('Invalid config payload.', 422);
        }

        return $this->success('Configuration updated successfully', [
            'configs' => $this->userConfigService->updateConfigs($userId, $configs),
        ]);
    }
}
