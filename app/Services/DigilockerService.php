<?php

namespace App\Services;

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

        $queryParameters = [
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'state' => 'redirect_uri',
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
            'scope' => 'openid'
        ];

        $url = config('services.digilocker.api_base_url') . "/oauth2/1/authorize?" . http_build_query($queryParameters);

        return [
            'url' => $url,
            'code_verifier' => $codeVerifier,
            'code_challenge' => $codeChallenge
        ];
    }
}
