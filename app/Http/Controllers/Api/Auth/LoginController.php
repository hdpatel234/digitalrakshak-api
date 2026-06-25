<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\SocialLoginProvider;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Services\DigilockerService;
use App\Services\LoginService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class LoginController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected LoginService $loginService,
        protected DigilockerService $digilockerService
    ) {}

    public function login(LoginRequest $request)
    {
        addInfoLog("Login request");

        $result = $this->loginService->login($request);

        if ($result['status'] == false) {
            return $this->error($result['message']);
        }

        return $this->success($result['message'], $result['data']);
    }

    public function socialLogin(Request $request)
    {
        addInfoLog("Social Login request");

        $provider = $request->provider;

        if ($provider === SocialLoginProvider::DIGILOCKER->value) {
            $authData = $this->digilockerService->getAuthorizationUrl();

            return $this->success('Authorization URL generated successfully.', ['url' => $authData['url']]);
        }

        return $this->error("Provider not supported");
    }
}
