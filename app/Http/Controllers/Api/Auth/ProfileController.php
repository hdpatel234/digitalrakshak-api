<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Api\Auth\UpdateProfileRequest;
use App\Traits\ApiResponse;
use App\Services\UserService;

class ProfileController extends BaseController
{
    use ApiResponse;
    protected $userServcice;
    public function __construct(UserService $userServcice)
    {
        $this->userServcice = $userServcice;
    }

    public function me(\Illuminate\Http\Request $request)
    {
        $user = $request->user();
        $response = $user->toArray();

        // Avatar is already prefixed in User::toArray()

        $userConfigService = app(\App\Services\UserConfigService::class);
        $resolvedConfigs = $userConfigService->getResolvedConfigs($user->id);
        $configKeyValue = [];
        foreach ($resolvedConfigs as $resolvedConfig) {
            if (isset($resolvedConfig['key'])) {
                $configKeyValue[(string) $resolvedConfig['key']] = $resolvedConfig['value'] ?? null;
            }
        }

        $response['config'] = $configKeyValue;
        $response['roles'] = $user->getRoleNames()->values()->all();
        $response['permissions'] = $user->getAllPermissions()->pluck('name')->values()->all();

        return $this->success('auth.get_profile.response_messages.profile_success', $response);
    }

    public function getPermissions(\Illuminate\Http\Request $request)
    {
        $user = $request->user();
        $permissions = $user->getAllPermissions()->pluck('name')->values()->all();

        return $this->success('Permissions retrieved successfully', $permissions);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = $request->user();
            $response = $this->userServcice->updateProfile(
                $user,
                $request->validated(),
                $request->file('avatar')
            );

            $responseData = $response->toArray();
            // Avatar is already prefixed in User::toArray()

            return $this->success('auth.update_profile.response_messages.profile_updated_success', $responseData);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
