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

    public function me()
    {
        $response = auth()->user();

        if (!empty($response['avatar'])) {
            $response['avatar'] = rtrim((string) config('app.url'), '/') . '/storage/' . ltrim((string) $response['avatar'], '/');
        }

        return $this->success('auth.get_profile.response_messages.profile_success', $response);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = auth()->user();
            $response = $this->userServcice->updateProfile(
                $user,
                $request->validated(),
                $request->file('avatar')
            );

            $responseData = $response->toArray();
            if (!empty($responseData['avatar'])) {
                $responseData['avatar'] = rtrim((string) config('app.url'), '/') . '/storage/' . ltrim((string) $responseData['avatar'], '/');
            }

            return $this->success('auth.update_profile.response_messages.profile_updated_success', $responseData);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
