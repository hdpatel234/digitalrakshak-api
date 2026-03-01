<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Services\BaseService;
use App\Repositories\UserSessionRepository;

class LogoutService extends BaseService
{
    protected $userRepository;
    protected $userSessionRepository;
    public function __construct
    (
        UserRepository $repository,
        UserSessionRepository $userSessionRepository
    ) {
        $this->userRepository = $repository;
        $this->userSessionRepository = $userSessionRepository;
    }

    public function logout($user)
    {
        $return_array = [];
        $return_array["status"] = true;
        $return_array["message"] = 'auth.logout.response_messages.logout_success';
        $return_array["data"] = [];

        try {
            $currentToken = $user->currentAccessToken();

            if ($currentToken) {
                $tokenId = $currentToken->id;
                $this->userSessionRepository->markInactive($tokenId);
                $currentToken->delete();
            }

            return $return_array;

        } catch (\Exception $e) {
            $return_array["status"] = false;
            $return_array["message"] = $e->getMessage();
            return $return_array;
        }
    }

    public function logoutAll($user)
    {
        $return_array = [];
        $return_array["status"] = true;
        $return_array["message"] = 'auth.logout.response_messages.logout_all_success';
        $return_array["data"] = [];

        $user->tokens()->delete();

        return $return_array;
    }
}
