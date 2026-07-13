<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\Http;
use App\Repositories\UserSessionRepository;
use Illuminate\Support\Facades\Log;

class LoginService extends BaseService
{
    public function __construct
    (
        protected UserRepository $userRepository,
        protected UserConfigService $userConfigService,
        protected UserSessionRepository $userSessionRepository
    ) {
    }

    public function login($request)
    {
        $return_array = [];
        $return_array['status'] = false;
        $return_array['message'] = '';
        $return_array['data'] = [];

        try {

            $response = Http::asForm()->post(config('app.url') . '/oauth/token', [
                'grant_type' => 'password',
                'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
                'client_secret' => env('PASSPORT_PASSWORD_CLIENT_SECRET'),
                'username' => $request->email,
                'password' => $request->password,
                'scope' => '',
            ]);

            if ($response->failed()) {
                $return_array['status'] = false;
                $return_array['message'] = 'auth.login.response_messages.invalid_credentials';
                return $return_array;
            }

            $tokenData = $response->json();

            $user = $this->userRepository->getByEmail($request->email);

            if (!$user) {
                $return_array['status'] = false;
                $return_array['message'] = 'auth.login.response_messages.user_not_found';
                return $return_array;
            }

            if ($user->{$this->userRepository->isActive()} !== \App\Enums\UserStatus::ACTIVE) {
                $return_array['status'] = false;
                $return_array['message'] = 'auth.login.response_messages.account_inactive';
                return $return_array;
            }


            $accessTokenId = null;

            if (isset($tokenData['access_token'])) {
                $jwtParts = explode('.', $tokenData['access_token']);
                if (count($jwtParts) === 3) {
                    $payload = json_decode(base64_decode($jwtParts[1]));
                    $accessTokenId = $payload->jti ?? null;
                }
            }

            $this->userSessionRepository->create([
                'user_id' => $user->{$this->userRepository->id()},
                'access_token_id' => $accessTokenId,
                'ip_address' => $request->ip ?? request()->ip(),
                'browser' => $request->browser ?? request()->header('User-Agent'),
                'os' => $request->os ?? null,
                'device' => $request->device ?? request()->header('User-Agent'),
            ]);

            $lastLoginData = [
                $this->userRepository->lastLoginAt() => $user->{$this->userRepository->lastLoginAt()},
                $this->userRepository->lastLoginIp() => $user->{$this->userRepository->lastLoginIp()},
                $this->userRepository->lastLoginBrowser() => $user->{$this->userRepository->lastLoginBrowser()},
                $this->userRepository->lastLoginDevice() => $user->{$this->userRepository->lastLoginDevice()},
            ];

            $user->update([
                $this->userRepository->lastLoginAt() => now(),
                $this->userRepository->lastLoginIp() => $request->ip ?? request()->ip(),
                $this->userRepository->lastLoginBrowser() => $request->browser ?? request()->header('User-Agent'),
                $this->userRepository->lastLoginDevice() => $request->device ?? request()->header('User-Agent'),
                $this->userRepository->lastLoginOs() => $request->os ?? null,
            ]);

            $resolvedConfigs = $this->userConfigService->getResolvedConfigs($user->{$this->userRepository->id()});
            $configKeyValue = [];
            foreach ($resolvedConfigs as $resolvedConfig) {
                if (!isset($resolvedConfig['key'])) {
                    continue;
                }

                $configKeyValue[(string) $resolvedConfig['key']] = $resolvedConfig['value'] ?? null;
            }

            $roles = $user->getRoleNames()->values()->all();
            $permissions = $user->getAllPermissions()
                ->pluck('name')
                ->values()
                ->all();

            $return_array['data'] = [
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'],
                'expires_in' => $tokenData['expires_in'],
                'token_type' => 'Bearer',
                'user' => [
                    $this->userRepository->id() => $user->{$this->userRepository->id()},
                    $this->userRepository->firstName() => $user->{$this->userRepository->firstName()},
                    $this->userRepository->lastName() => $user->{$this->userRepository->lastName()},
                    $this->userRepository->email() => $user->{$this->userRepository->email()},
                    $this->userRepository->lastLoginAt() => $lastLoginData[$this->userRepository->lastLoginAt()],
                    $this->userRepository->lastLoginIp() => $lastLoginData[$this->userRepository->lastLoginIp()],
                    $this->userRepository->lastLoginBrowser() => $lastLoginData[$this->userRepository->lastLoginBrowser()],
                    $this->userRepository->lastLoginDevice() => $lastLoginData[$this->userRepository->lastLoginDevice()],
                    $this->userRepository->isActive() => $user->{$this->userRepository->isActive()},
                    $this->userRepository->isAdmin() => $user->{$this->userRepository->isAdmin()},
                    $this->userRepository->avatar() => !empty($user->{$this->userRepository->avatar()}) ? rtrim((string) config('app.url'), '/') . '/storage/' . ltrim((string) $user->{$this->userRepository->avatar()}, '/') : '',
                ],
                'config' => $configKeyValue,
                'roles' => $roles,
                'permissions' => $permissions,
            ];

            $return_array['status'] = true;
            $return_array['message'] = 'auth.login.response_messages.login_success';

            return $return_array;
        } catch (\Exception $e) {
            $return_array['status'] = false;
            $return_array['message'] = $e->getMessage();
            return $return_array;
        }
    }

    public function socialLogin($user, $request)
    {
        $return_array = [];
        $return_array['status'] = false;
        $return_array['message'] = '';
        $return_array['data'] = [];

        try {
            if ($user->{$this->userRepository->isActive()} !== \App\Enums\UserStatus::ACTIVE) {
                $return_array['status'] = false;
                $return_array['message'] = 'auth.login.response_messages.account_inactive';
                return $return_array;
            }

            // Create personal access token since we bypass password grant
            $tokenResult = $user->createToken('SocialLogin');
            
            $tokenData = [
                'access_token' => $tokenResult->accessToken,
                'refresh_token' => null, // Personal access tokens don't have refresh tokens
                'expires_in' => $tokenResult->token->expires_at ? $tokenResult->token->expires_at->diffInSeconds(now()) : 31536000,
            ];

            $accessTokenId = $tokenResult->token->id;

            $this->userSessionRepository->create([
                'user_id' => $user->{$this->userRepository->id()},
                'access_token_id' => $accessTokenId,
                'ip_address' => $request->ip ?? request()->ip(),
                'browser' => $request->browser ?? request()->header('User-Agent'),
                'os' => $request->os ?? null,
                'device' => $request->device ?? request()->header('User-Agent'),
            ]);

            $lastLoginData = [
                $this->userRepository->lastLoginAt() => $user->{$this->userRepository->lastLoginAt()},
                $this->userRepository->lastLoginIp() => $user->{$this->userRepository->lastLoginIp()},
                $this->userRepository->lastLoginBrowser() => $user->{$this->userRepository->lastLoginBrowser()},
                $this->userRepository->lastLoginDevice() => $user->{$this->userRepository->lastLoginDevice()},
            ];

            $user->update([
                $this->userRepository->lastLoginAt() => now(),
                $this->userRepository->lastLoginIp() => $request->ip ?? request()->ip(),
                $this->userRepository->lastLoginBrowser() => $request->browser ?? request()->header('User-Agent'),
                $this->userRepository->lastLoginDevice() => $request->device ?? request()->header('User-Agent'),
                $this->userRepository->lastLoginOs() => $request->os ?? null,
            ]);

            $resolvedConfigs = $this->userConfigService->getResolvedConfigs($user->{$this->userRepository->id()});
            $configKeyValue = [];
            foreach ($resolvedConfigs as $resolvedConfig) {
                if (!isset($resolvedConfig['key'])) {
                    continue;
                }

                $configKeyValue[(string) $resolvedConfig['key']] = $resolvedConfig['value'] ?? null;
            }

            $roles = $user->getRoleNames()->values()->all();
            $permissions = $user->getAllPermissions()
                ->pluck('name')
                ->values()
                ->all();

            $return_array['data'] = [
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'],
                'expires_in' => $tokenData['expires_in'],
                'token_type' => 'Bearer',
                'user' => [
                    $this->userRepository->id() => $user->{$this->userRepository->id()},
                    $this->userRepository->firstName() => $user->{$this->userRepository->firstName()},
                    $this->userRepository->lastName() => $user->{$this->userRepository->lastName()},
                    $this->userRepository->email() => $user->{$this->userRepository->email()},
                    $this->userRepository->lastLoginAt() => $lastLoginData[$this->userRepository->lastLoginAt()],
                    $this->userRepository->lastLoginIp() => $lastLoginData[$this->userRepository->lastLoginIp()],
                    $this->userRepository->lastLoginBrowser() => $lastLoginData[$this->userRepository->lastLoginBrowser()],
                    $this->userRepository->lastLoginDevice() => $lastLoginData[$this->userRepository->lastLoginDevice()],
                    $this->userRepository->isActive() => $user->{$this->userRepository->isActive()},
                    $this->userRepository->isAdmin() => $user->{$this->userRepository->isAdmin()},
                    $this->userRepository->avatar() => !empty($user->{$this->userRepository->avatar()}) ? rtrim((string) config('app.url'), '/') . '/storage/' . ltrim((string) $user->{$this->userRepository->avatar()}, '/') : '',
                ],
                'config' => $configKeyValue,
                'roles' => $roles,
                'permissions' => $permissions,
            ];

            $return_array['status'] = true;
            $return_array['message'] = 'auth.login.response_messages.login_success';

            return $return_array;
        } catch (\Exception $e) {
            $return_array['status'] = false;
            $return_array['message'] = $e->getMessage();
            return $return_array;
        }
    }
}
