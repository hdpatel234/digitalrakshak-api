<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DigilockerService
{
    /**
     * Generate Digilocker authorization URL
     *
     * @return array
     */
    public function getAuthorizationUrl()
    {
        $clientId = config('services.digilocker.client_id');
        $redirectUri = config('services.digilocker.redirect_uri');

        $codeVerifier = bin2hex(random_bytes(32));

        $codeChallenge = rtrim(
            strtr(
                base64_encode(
                    hash('sha256', $codeVerifier, true)
                ),
                '+/',
                '-_'
            ),
            '='
        );

        $state = Str::random(40);

        $queryParameters = [
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
            'scope' => 'openid'
        ];

        $url = config('services.digilocker.api_base_url') . "/oauth2/1/authorize?" . http_build_query($queryParameters);

        return [
            'url' => $url,
            'code_verifier' => $codeVerifier,
            'code_challenge' => $codeChallenge,
            'state' => $state
        ];
    }

    /**
     * Exchange authorization code for access token
     *
     * @param string $code
     * @param string $codeVerifier
     * @return array
     */
    public function exchangeToken(string $code, string $codeVerifier)
    {
        $response = Http::asForm()->post(config('services.digilocker.api_base_url') . '/oauth2/1/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.digilocker.client_id'),
            'client_secret' => config('services.digilocker.client_secret'),
            'redirect_uri' => config('services.digilocker.redirect_uri'),
            'code' => $code,
            'code_verifier' => $codeVerifier,
        ]);

        if ($response->failed()) {
            return ['status' => false, 'message' => $response->body()];
        }

        return ['status' => true, 'data' => $response->json()];
    }

    /**
     * Get user profile from Digilocker
     *
     * @param string $accessToken
     * @return array
     */
    public function getUserProfile(string $accessToken)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get(config('services.digilocker.api_base_url') . '/oauth2/1/user');

        if ($response->failed()) {
            return ['status' => false, 'message' => $response->body()];
        }

        return ['status' => true, 'data' => $response->json()];
    }
}
