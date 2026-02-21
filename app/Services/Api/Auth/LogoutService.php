<?php

namespace App\Services\Api\Auth;

use App\Repositories\UserRepository;
use App\Services\BaseService;

class LogoutService extends BaseService
{
    public function __construct(UserRepository $repository)
    {
        return parent::__construct($repository);
    }

    public function logout($user)
    {
        $return_array = [];
        $return_array["status"] = true;
        $return_array["message"] = __('auth/logout.response_messages.logout_success');
        $return_array["data"] = [];

        $user->currentAccessToken()->delete();

        return $return_array;
    }

    public function logoutAll($user)
    {
        $return_array = [];
        $return_array["status"] = true;
        $return_array["message"] = __('auth/logout.response_messages.logout_all_success');
        $return_array["data"] = [];

        $user->tokens()->delete();

        return $return_array;
    }
}
