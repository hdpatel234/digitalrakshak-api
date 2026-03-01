<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Http;
use App\Services\BaseService;

class RefreshTokenService extends BaseService
{
    protected $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function refreshToken($refreshToken)
    {
        $return_array = [];
        $return_array['status'] = false;
        $return_array['message'] = '';
        $return_array['data'] = [];

        try {
            $response = Http::asForm()->post(config('app.url') . '/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
                'client_secret' => env('PASSPORT_PASSWORD_CLIENT_SECRET'),
                'scope' => '',
            ]);

            if ($response->failed()) {
                $return_array['status'] = false;
                $return_array['message'] = 'auth.refresh_token.response_messages.invalid_refresh_token';
                return $return_array;
            }

            $return_array['status'] = true;
            $return_array['message'] = 'auth.refresh_token.response_messages.refresh_success';
            $return_array['data'] = $response->json();

            return $return_array;
        } catch (\Exception $e) {
            $return_array['status'] = false;
            $return_array['message'] = $e->getMessage();
            return $return_array;
        }
    }
}