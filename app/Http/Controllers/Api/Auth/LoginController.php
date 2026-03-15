<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Api\Auth\LoginRequest;
use App\Services\LoginService;
use App\Traits\ApiResponse;

class LoginController extends BaseController
{
    use ApiResponse;

    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function login(LoginRequest $request)
    {
        addInfoLog("Login request");

        $result = $this->loginService->login($request);

        if ($result['status'] == false) {
            return $this->error($result['message']);
        }

        return $this->success($result['message'], $result['data']);
    }
}
