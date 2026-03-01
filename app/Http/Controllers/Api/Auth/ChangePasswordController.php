<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Api\Auth\ChangePasswordRequest;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends BaseController
{
    use ApiResponse;
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = auth()->user();
            $data = $request->validated();

            if (!$user || !Hash::check($data['current_password'], $user->password)) {
                return $this->error('auth.change_password.response_messages.invalid_current_password');
            }

            $user->password = $data['new_password'];
            $user->save();

            return $this->success('auth.change_password.response_messages.password_changed_success');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
