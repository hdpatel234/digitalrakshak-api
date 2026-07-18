<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Repositories\ProviderApiConfigRepository;
use App\Repositories\ServiceProviderRepository;
use App\Repositories\ApiProviderLogRepository;

class ProteanService
{
    protected ?object $config = null;
    protected string $environment = 'sandbox';
    public function __construct(
        protected ProviderApiConfigRepository $providerApiConfigRepository,
        protected ServiceProviderRepository $serviceProviderRepository,
        protected ApiProviderLogRepository $apiProviderLogRepository
    ) {
        $this->loadConfiguration();
    }

    /**
     * Load the appropriate active configuration from the database.
     */
    protected function loadConfiguration(): void
    {
        $this->environment = config('app.env') === 'production' ? 'production' : 'sandbox';

        $configQuery = $this->providerApiConfigRepository->query()
            ->join(
                'service_providers',
                'provider_api_configs.' . $this->providerApiConfigRepository->providerId(),
                '=',
                'service_providers.' . $this->serviceProviderRepository->id()
            );

        $config = (clone $configQuery)
            ->where('service_providers.' . $this->serviceProviderRepository->providerCode(), 'protean')
            ->where('provider_api_configs.' . $this->providerApiConfigRepository->environment(), $this->environment)
            ->select('provider_api_configs.*')
            ->first();

        // Fallback to any active configuration if environment match is not found
        if (!$config) {
            $config = clone $configQuery;
            $config = $config->where('service_providers.' . $this->serviceProviderRepository->providerCode(), 'protean')
                ->where('provider_api_configs.' . $this->providerApiConfigRepository->status(), 'active')
                ->select('provider_api_configs.*')
                ->first();
        }

        $this->config = $config;
    }

    /**
     * Get or fetch a cached active OAuth access token.
     */
    public function getAccessToken(): string
    {
        if (!$this->config) {
            throw new Exception("Protean API integration is not configured in the database.");
        }

        // Return cached token if valid
        if (!empty($this->config->api_token) && !empty($this->config->token_expiry)) {
            $expiry = Carbon::parse($this->config->token_expiry);
            if ($expiry->isFuture()) {
                return $this->config->api_token;
            }
        }

        return $this->fetchNewAccessToken();
    }

    /**
     * Fetch a new OAuth access token using credentials and store in database cache.
     */
    protected function fetchNewAccessToken(): string
    {
        $url = rtrim((string)$this->config->base_url, '/') . '/v1/oauth/token';
        $auth = base64_encode($this->config->api_key . ':' . $this->config->api_secret);

        $response = Http::asForm()
            ->withHeaders([
                'Authorization' => 'Basic ' . $auth,
            ])
            ->post($url, [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->failed()) {
            Log::error('Protean OAuth request failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            throw new Exception("Failed to retrieve Protean access token: HTTP " . $response->status());
        }

        $data = $response->json();
        $token = $data['access_token'] ?? null;
        $expiresIn = $data['expires_in'] ?? 3599; // Default to 1 hour minus margin

        if (!$token) {
            throw new Exception("Access token not found in Protean response: " . $response->body());
        }

        // Cache the token with a safety margin of 60 seconds
        $expiry = now()->addSeconds((int)$expiresIn - 60);

        $this->providerApiConfigRepository->update($this->config->id, [
            $this->providerApiConfigRepository->apiToken() => $token,
            $this->providerApiConfigRepository->tokenExpiry() => $expiry,
            $this->providerApiConfigRepository->updatedAt() => now(),
        ]);

        // Refresh internal configuration cache
        $this->config->api_token = $token;
        $this->config->token_expiry = $expiry->toDateTimeString();

        return $token;
    }

    /**
     * Retrieve the RSA public key file content from storage.
     */
    protected function getPublicKey(): string
    {
        $path = storage_path('app/' . $this->config->ssl_cert_path);
        if (!file_exists($path)) {
            throw new Exception("Protean Public Key file not found at " . $path);
        }
        return file_get_contents($path);
    }

    /**
     * Retrieve the Server private key file content from storage.
     */
    protected function getPrivateKey(): string
    {
        $path = storage_path('app/' . $this->config->ssl_key_path);
        if (!file_exists($path)) {
            throw new Exception("Server Private Key file not found at " . $path);
        }
        return file_get_contents($path);
    }

    /**
     * Cryptographic: AES-256-GCM symmetric encryption matching test requirements.
     */
    protected function encryptAES(string $plainText, string $symmetricKey): string
    {
        $salt = openssl_random_pseudo_bytes(16);
        $iv = openssl_random_pseudo_bytes(12);

        $aesKey = openssl_pbkdf2($symmetricKey, $salt, 32, 65536, 'sha256');

        $tag = '';
        $cipherText = openssl_encrypt($plainText, 'aes-256-gcm', $aesKey, OPENSSL_RAW_DATA, $iv, $tag);

        $cipherTextWithIvSalt = $iv . $salt . $cipherText . $tag;

        return base64_encode($cipherTextWithIvSalt);
    }

    /**
     * Cryptographic: AES-256-GCM symmetric decryption.
     */
    protected function decryptAES(string $encodedData, string $symmetricKey): string
    {
        $decodedData = base64_decode($encodedData);

        $iv = substr($decodedData, 0, 12);
        $salt = substr($decodedData, 12, 16);
        $tag = substr($decodedData, -16);
        $ciphertext = substr($decodedData, 28, -16);

        $aesKey = openssl_pbkdf2($symmetricKey, $salt, 32, 65536, 'sha256');

        return openssl_decrypt($ciphertext, 'aes-256-gcm', $aesKey, OPENSSL_RAW_DATA, $iv, $tag);
    }

    /**
     * Cryptographic: RSA Public Key Encryption via phpseclib3 with SHA-256 OAEP padding.
     */
    protected function encryptRSA(string $data): string
    {
        $publicKeyContent = $this->getPublicKey();
        $key = PublicKeyLoader::load($publicKeyContent);

        if (!$key instanceof \phpseclib3\Crypt\RSA\PublicKey) {
            throw new Exception("The configured Protean Public Key is not a valid RSA key.");
        }

        $key = $key->withPadding(RSA::ENCRYPTION_OAEP)
            ->withHash('sha256')
            ->withMGFHash('sha256');

        return base64_encode($key->encrypt($data));
    }

    /**
     * Cryptographic: RSA Private Key Decryption via phpseclib3.
     */
    protected function decryptRSA(string $encryptedData): string
    {
        $privateKeyContent = $this->getPrivateKey();
        $key = PublicKeyLoader::load($privateKeyContent);

        if (!$key instanceof \phpseclib3\Crypt\RSA\PrivateKey) {
            throw new Exception("The configured Server Private Key is not a valid RSA key.");
        }

        $key = $key->withPadding(RSA::ENCRYPTION_OAEP)
            ->withHash('sha256')
            ->withMGFHash('sha256');

        return $key->decrypt(base64_decode($encryptedData));
    }

    /**
     * Cryptographic: Calculate HMAC-SHA256 signature.
     */
    protected function calculateHmac(string $data, string $symmetricKey): string
    {
        $hash = hash_hmac('sha256', $data, $symmetricKey, true);
        return base64_encode($hash);
    }

    /**
     * Helper to generate a unique UUID v4.
     */
    protected function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Main HTTP request router with encryption/decryption handling.
     */
    public function sendRequest(string $endpoint, array $payload, bool $encryptPayload = false): array
    {
        $token = $this->getAccessToken();
        $baseUrl = rtrim((string)$this->config->base_url, '/');
        $url = $baseUrl . $endpoint;

        $requestBody = $payload;
        $symmetricKey = null;

        if ($encryptPayload) {
            // 16-byte secure random symmetric key
            $symmetricKey = openssl_random_pseudo_bytes(16);
            $plainJson = json_encode($payload);

            $encryptedData = $this->encryptAES($plainJson, $symmetricKey);
            $encryptedSymmetricKey = $this->encryptRSA($symmetricKey);
            $hmac = $this->calculateHmac($plainJson, $symmetricKey);

            $requestBody = [
                'data' => $encryptedData,
                'version' => $this->config->api_version ?? '1.0.0',
                'symmetricKey' => $encryptedSymmetricKey,
                'hash' => $hmac,
                'timestamp' => date('Y-m-d\TH:i:s.v'),
                'requestId' => $this->generateUUID()
            ];
        }

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'apikey' => $this->config->api_key,
            'Content-Type' => 'application/json',
        ];

        // Perform request
        $startTime = microtime(true);
        $response = Http::withHeaders($headers)
            ->timeout($this->config->timeout_seconds ?? 60)
            ->post($url, $requestBody);
        $duration = microtime(true) - $startTime;

        $statusCode = $response->status();
        $responseData = $response->json() ?: ['raw' => $response->body()];

        // Audit performance & log API execution
        $this->logApiCall($endpoint, 'POST', $payload, $responseData, $statusCode, $duration);

        // Process response
        if ($response->failed()) {
            if (isset($responseData['data']) && isset($responseData['symmetricKey'])) {
                try {
                    $decryptedError = $this->decryptResponse($responseData);
                    return [
                        'success' => false,
                        'status_code' => $statusCode,
                        'error' => 'API Error Decrypted',
                        'data' => $decryptedError
                    ];
                } catch (Exception $e) {
                    return [
                        'success' => false,
                        'status_code' => $statusCode,
                        'error' => 'API Error (Decryption failed: ' . $e->getMessage() . ')',
                        'raw' => $responseData
                    ];
                }
            }

            return [
                'success' => false,
                'status_code' => $statusCode,
                'error' => $responseData['message'] ?? 'API request failed.',
                'raw' => $responseData
            ];
        }

        // Decrypt success response if encrypted
        if (isset($responseData['data']) && isset($responseData['symmetricKey'])) {
            try {
                $decryptedSuccess = $this->decryptResponse($responseData);
                return [
                    'success' => true,
                    'status_code' => $statusCode,
                    'data' => $decryptedSuccess
                ];
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'status_code' => $statusCode,
                    'error' => 'Decryption of success response failed: ' . $e->getMessage(),
                    'raw' => $responseData
                ];
            }
        }

        return [
            'success' => true,
            'status_code' => $statusCode,
            'data' => $responseData
        ];
    }

    /**
     * Decrypt an encrypted response from Protean.
     */
    protected function decryptResponse(array $encryptedResponse): array
    {
        if (empty($encryptedResponse['symmetricKey']) || empty($encryptedResponse['data']) || empty($encryptedResponse['hash'])) {
            throw new Exception("Response is missing encryption metadata fields.");
        }

        // Decrypt the symmetric key from the response using private key
        $decryptedSymmetricKey = $this->decryptRSA($encryptedResponse['symmetricKey']);
        if ($decryptedSymmetricKey === false) {
            throw new Exception("RSA decryption of response symmetric key failed.");
        }

        // Decrypt actual payload
        $decryptedData = $this->decryptAES($encryptedResponse['data'], $decryptedSymmetricKey);
        if ($decryptedData === false) {
            throw new Exception("AES decryption of response payload failed.");
        }

        // HMAC verification
        $calculatedHmac = $this->calculateHmac($decryptedData, $decryptedSymmetricKey);
        if ($calculatedHmac !== $encryptedResponse['hash']) {
            throw new Exception("HMAC signature verification failed on the response payload.");
        }

        $jsonData = json_decode($decryptedData, true);
        if ($jsonData === null) {
            throw new Exception("Failed to parse decrypted data as JSON: " . $decryptedData);
        }

        return $jsonData;
    }

    /**
     * Log execution to tblapi_provider_logs table.
     */
    protected function logApiCall(string $endpoint, string $method, array $request, array $response, int $code, float $duration): void
    {
        try {
            $this->apiProviderLogRepository->create([
                $this->apiProviderLogRepository->apiProviderId() => $this->config->provider_id,
                $this->apiProviderLogRepository->endpoint() => $endpoint,
                $this->apiProviderLogRepository->method() => $method,
                $this->apiProviderLogRepository->request() => json_encode($request),
                $this->apiProviderLogRepository->response() => json_encode($response),
                $this->apiProviderLogRepository->responseCode() => $code,
                $this->apiProviderLogRepository->duration() => $duration,
                $this->apiProviderLogRepository->isSuccessful() => ($code >= 200 && $code < 300) ? 1 : 0,
                $this->apiProviderLogRepository->createdAt() => now(),
                $this->apiProviderLogRepository->updatedAt() => now(),
            ]);

            // Update stats on config
            $this->providerApiConfigRepository->query()
                ->where($this->providerApiConfigRepository->id(), $this->config->id)
                ->increment($code >= 200 && $code < 300 ? $this->providerApiConfigRepository->successfulCalls() : $this->providerApiConfigRepository->failedCalls(), 1, [
                    $this->providerApiConfigRepository->totalCalls() => DB::raw($this->providerApiConfigRepository->totalCalls() . ' + 1'),
                    $this->providerApiConfigRepository->updatedAt() => now()
                ]);
        } catch (Exception $e) {
            Log::warning("Failed to record Protean api provider log: " . $e->getMessage());
        }
    }

    // ==========================================
    // API Implementations
    // ==========================================

    /**
     * 1. Mobile Silent Verification (Consent Free Phone KYC)
     */
    public function silentVerification(string $mobileNumber, string $additionalDetails = 'yes'): array
    {
        return $this->sendRequest(
            '/api/v1/protean/phones/phone-kyc-non-consent',
            [
                'mobileNumber' => $mobileNumber,
                'additionalDetails' => $additionalDetails
            ],
            true
        );
    }

    /**
     * 2. Mobile Verification with OTP - Generate OTP
     */
    public function generateOtp(string $mobileNumber, string $countryCode = '91'): array
    {
        return $this->sendRequest(
            '/api/v1/protean/phone/generateOtp',
            [
                'countryCode' => $countryCode,
                'mobileNumber' => $mobileNumber
            ]
        );
    }

    /**
     * 3. Geo Fencing
     */
    public function geoFencing(string $ip, string $state, string $country = 'IN'): array
    {
        return $this->sendRequest(
            '/api/v1/protean/patrons/riskscores',
            [
                'task' => 'geoFencing',
                'essentials' => [
                    'ip' => $ip,
                    'country' => $country,
                    'state' => $state
                ]
            ]
        );
    }

    /**
     * 4. Reverse Geocode
     */
    public function reverseGeocode(string $latitude, string $longitude): array
    {
        return $this->sendRequest(
            '/api/v1/protean/geocoding/reverse-geocode',
            [
                'latitude' => $latitude,
                'longitude' => $longitude
            ]
        );
    }

    /**
     * 5. KYC OCR Plus
     */
    public function kycOcr(array $imageUrls): array
    {
        return $this->sendRequest(
            '/api/v1/protean/utility/single-kyc',
            [
                'url' => $imageUrls
            ]
        );
    }

    /**
     * 6.1 Dynamic Bank Account Verification (Advanced)
     */
    public function bankVerify(string $beneficiaryAccount, string $beneficiaryIFSC): array
    {
        return $this->sendRequest(
            '/api/v1/protean-variablepennydrop/bankaccountverifications/advancedverification',
            [
                'beneficiaryAccount' => $beneficiaryAccount,
                'beneficiaryIFSC' => $beneficiaryIFSC
            ]
        );
    }

    /**
     * 6.2 Dynamic Bank Account Verification - Verify Amount
     */
    public function bankVerifyAmount(float $amount, string $referenceId): array
    {
        return $this->sendRequest(
            '/api/v1/protean-variablepennydrop/bankaccountverification/verifytransferadvanced',
            [
                'amount' => $amount,
                'referenceid' => $referenceId
            ]
        );
    }

    /**
     * 7. Shop and Establishment
     */
    public function shopEstablishment(string $registrationNumber, string $state): array
    {
        return $this->sendRequest(
            '/api/v1/protean/shop-establishment',
            [
                'registrationNumber' => $registrationNumber,
                'state' => $state
            ]
        );
    }

    /**
     * 8. EPF UAN Validation
     */
    public function epfUanValidation(string $uan): array
    {
        return $this->sendRequest(
            '/api/v1/protean/fetch-employment-history',
            [
                'uan' => $uan
            ]
        );
    }
}
