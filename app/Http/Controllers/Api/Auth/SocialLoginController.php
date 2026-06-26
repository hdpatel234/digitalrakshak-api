<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\SocialLoginProvider;
use App\Models\User;
use App\Services\DigilockerService;
use App\Services\LoginService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
            return $this->error('Missing code or state parameter');
        }

        $codeVerifier = Cache::get('digilocker_state_' . $state);

        if (!$codeVerifier) {
            return $this->error('Invalid or expired state parameter');
        }

        Cache::forget('digilocker_state_' . $state);

        $tokenResponse = $this->digilockerService->exchangeToken($code, $codeVerifier);

        if (!$tokenResponse['status']) {
            return $this->error('Failed to exchange token: ' . $tokenResponse['message']);
        }

        $accessToken = $tokenResponse['data']['access_token'] ?? null;
        if (!$accessToken) {
            return $this->error('Access token not found in response');
        }

        $profileResponse = $this->digilockerService->getUserProfile($accessToken);

        if (!$profileResponse['status']) {
            return $this->error('Failed to get user profile: ' . $profileResponse['message']);
        }

        $profile = $profileResponse['data'];

        Log::info("DigiLocker profile", $profile);

        $digilockerId = $profile['digilockerid'] ?? null;

        Log::info("DigiLocker ID: ", [$digilockerId]);

        $email = $profile['email'] ?? null;

        Log::info("DigiLocker email: ", [$email]);

        $phone = $profile['mobile'] ?? null;

        Log::info("DigiLocker phone: ", [$phone]);

        if (!$digilockerId) {
            return $this->error('DigiLocker ID not found in profile', 400, []);
        }

        $user = User::where('last_login_provider_id', $digilockerId)->first();

        if (!$user) {
            $userQuery = User::query();

            if ($email || $phone) {
                $userQuery->where(function ($query) use ($email, $phone) {
                    if ($email) {
                        $query->orWhere('email', $email);
                    }
                    if ($phone) {
                        $query->orWhere('phone', $phone);
                    }
                });
                $user = $userQuery->first();
            }

            if ($user) {
                $user->update([
                    'last_login_provider' => 'digilocker',
                    'last_login_provider_id' => $digilockerId,
                ]);
            }
        }

        if (!$user) {
            return $this->error('User not registered in system', 400, []);
        }

        $loginResponse = $this->loginService->socialLogin($user, $request);

        if (!$loginResponse['status']) {
            return $this->error($loginResponse['message']);
        }

        return $this->success($loginResponse['message'], $loginResponse['data']);
    }
}
