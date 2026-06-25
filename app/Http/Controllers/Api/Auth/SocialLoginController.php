<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\SocialLoginProvider;
use App\Models\User;
use App\Services\DigilockerService;
use App\Services\LoginService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SocialLoginController extends BaseController
{
    use ApiResponse;

    public function __construct(
        protected DigilockerService $digilockerService,
        protected LoginService $loginService
    ) {}

    public function googleLogin(Request $request)
    {
        addInfoLog("Google login request");
    }

    public function facebookLogin(Request $request)
    {
        addInfoLog("Facebook login request");
    }

    public function digiLockerLogin(Request $request)
    {
        addInfoLog("DigiLocker login request");

        $authData = $this->digilockerService->getAuthorizationUrl();

        Cache::put('digilocker_state_' . $authData['state'], $authData['code_verifier'], 300);

        return $this->success('Authorization URL generated successfully.', ['url' => $authData['url']]);
    }

    public function digiLockerCallback(Request $request)
    {
        addInfoLog("DigiLocker callback request");

        $code = $request->input('code');
        $state = $request->input('state');

        if (!$code || !$state) {
            return $this->error('Missing code or state parameter', 400, []);
        }

        $codeVerifier = Cache::get('digilocker_state_' . $state);

        if (!$codeVerifier) {
            return $this->error('Invalid or expired state parameter', 400, []);
        }

        Cache::forget('digilocker_state_' . $state);

        $tokenResponse = $this->digilockerService->exchangeToken($code, $codeVerifier);

        if (!$tokenResponse['status']) {
            return $this->error(
                'Failed to exchange token: ' . $tokenResponse['message'],
                400,
                []
            );
        }

        $accessToken = $tokenResponse['data']['access_token'] ?? null;

        if (!$accessToken) {
            return $this->error('Access token not found in response', 400, []);
        }

        $profileResponse = $this->digilockerService->getUserProfile($accessToken);

        if (!$profileResponse['status']) {
            return $this->error(
                'Failed to get user profile: ' . $profileResponse['message'],
                400,
                []
            );
        }

        $profile = $profileResponse['data'];
        $digilockerId = $profile['digilockerid'] ?? null;
        $email = $profile['email'] ?? null;
        $phone = $profile['phone'] ?? null;

        if (!$digilockerId) {
            return $this->error('DigiLocker ID not found in profile', 400, []);
        }

        $user = User::where('last_login_provider_id', $digilockerId)
            ->when($email, function ($query) use ($email) {
                $query->orWhere('email', $email);
            })
            ->when($phone, function ($query) use ($phone) {
                $query->orWhere('phone', $phone);
            })
            ->first();

        if (!$user) {
            return $this->error('User not registered in system', 400, []);
        }

        $user->update([
            'last_login_provider' => 'digilocker',
            'last_login_provider_id' => $digilockerId,
        ]);

        $loginResponse = $this->loginService->socialLogin($user, $request);

        if (!$loginResponse['status']) {
            return $this->error($loginResponse['message'], 400, []);
        }

        return $this->success(
            $loginResponse['message'],
            $loginResponse['data']
        );
    }
}
