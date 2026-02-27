<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\Http;

class LoginService extends BaseService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login($email, $password)
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
                'username' => $email,
                'password' => $password,
                'scope' => '',
            ]);

            if ($response->failed()) {
                $return_array['status'] = false;
                $return_array['message'] = __('auth/login.response_messages.invalid_credentials');
                return $return_array;
            }

            $tokenData = $response->json();

            $user = $this->userRepository->getByEmail($email);

            if (!$user) {
                $return_array['status'] = false;
                $return_array['message'] = __('auth/login.response_messages.user_not_found');
                return $return_array;
            }

            if($user->{$this->userRepository->isActive()} == false) {
                $return_array['status'] = false;
                $return_array['message'] = __('auth/login.response_messages.account_inactive');
                return $return_array;
            }

            $lastLoginData = [
                $this->userRepository->lastLoginAt() => $user->{$this->userRepository->lastLoginAt()},
                $this->userRepository->lastLoginIp() => $user->{$this->userRepository->lastLoginIp()},
                $this->userRepository->lastLoginBrowser() => $user->{$this->userRepository->lastLoginBrowser()},
                $this->userRepository->lastLoginDevice() => $user->{$this->userRepository->lastLoginDevice()},
            ];

            $user->update([
                $this->userRepository->lastLoginAt() => now(),
                $this->userRepository->lastLoginIp() => request()->ip(),
                $this->userRepository->lastLoginBrowser() => request()->header('User-Agent'),
                $this->userRepository->lastLoginDevice() => request()->header('User-Agent'),
            ]);

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
                ],
            ];

            $return_array['status'] = true;
            $return_array['message'] = __('auth/login.response_messages.login_success');

            return $return_array;
        } catch (\Exception $e) {
            $return_array['status'] = false;
            $return_array['message'] = $e->getMessage();
            return $return_array;
        }
    }
}
