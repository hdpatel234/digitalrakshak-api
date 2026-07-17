<?php

/**
 * Protean API Integration - Auto Key Generation Version
 * Automatically generates and manages RSA keys if they don't exist
 */

// In a PHP script:
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Set execution time limits
set_time_limit(120);
ini_set('default_socket_timeout', 120);

require 'vendor/autoload.php';

use phpseclib\Crypt\RSA;

// Configuration
define('API_BASE_URL', 'https://uat.risewithprotean.io');
define('OAUTH_TOKEN_URL', '/v1/oauth/token');

// New API endpoints
define('EPFO_EMPLOYEE_NAME_SEARCH_URL', '/api/v1/protean/epfo/employee-name-search');
define('EPF_UAN_LOOKUP_URL', '/api/v1/epfes/uan-lookup');
define('PASSPORT_VERIFICATION_URL', '/api/v1/protean/passport-verification');
define('VOTER_ID_VERIFICATION_URL', '/api/v1/protean/voter-id-verification');
define('DL_VERIFICATION_URL', '/api/v1/protean/dl-verification');
define('VEHICLE_RC_URL', '/api/v1/protean/vehicle-detailed-advanced');
define('VEHICLE_REVERSE_RC_URL', '/api/v1/protean/vehicle/reverse-search');
define('FAST_TAG_URL', '/api/v1/protean/vehicle/rc-to-fastag-details');
define('FASTTAG_LAST_LOCATION_URL', '/api/v1/protean/vehicle/fastag-history-details');
define('ELECTRICITY_BILL_URL', '/api/v1/protean/electricity-bill-authe');

// Default credentials
define('DEFAULT_API_KEY', 'f5iIy1xTuyEoGbE8GNHvcVBcz4vb6MUzEMo8jUOd3siOfnZg');
define('DEFAULT_SECRET_KEY', 'IGRHkqgEpGjtvtABjxZahIDur92FB76YhhqoixF1zSVKWMdAQzGOhqV1jkIA3KXV');
define('API_VERSION', '1.0.0');

// Key file paths
$publicKeyPath = __DIR__ . '/protean-public-key-2048.pem';
$privateKeyPath = __DIR__ . '/server-key-2048.pem'; // Changed to match generated key
$serverPublicKeyPath = __DIR__ . '/server-public-key-2048.pem';

// Function to generate new key pair
function generateKeyPair()
{
    try {
        $rsa = new RSA();
        // Use sha256 for OAEP padding - must match encryptRSA/decryptRSA functions
        $rsa->setHash('sha256');
        $rsa->setMGFHash('sha256');
        // Do NOT set PKCS1 public key format - use default PKCS8 (-----BEGIN PUBLIC KEY-----)
        // PKCS8 is universally compatible; PKCS1 causes decrypt failures with external APIs

        $keys = $rsa->createKey(2048);

        // Save private key
        file_put_contents(__DIR__ . '/server-key-2048.pem', $keys['privatekey']);
        // Save public key (for reference)
        file_put_contents(__DIR__ . '/server-public-key-2048.pem', $keys['publickey']);

        return [
            'success' => true,
            'private_key' => $keys['privatekey'],
            'public_key' => $keys['publickey'],
            'message' => "New RSA key pair generated successfully!"
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => "Failed to generate keys: " . $e->getMessage()
        ];
    }
}

// Function to check and load or generate private key
function getPrivateKey()
{
    return "-----BEGIN PRIVATE KEY-----
MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDEsDhqBX9VW2e2
z2tzcxKiJlc3yVVX4eomHr4rqpQbi8n0zp4fdqVHK3IRs+KhZ7F0c8wPgEZNRhH6
XZ5DK9q1+0FEK6Lj0O1ZKgVSvpB7oAEqsCbrbxucAts1UQowG2PZN1vzXKg//V6R
Y7tH26N283YgvqdLRmO7Je/3+cBUPWnLyeXNs3AMapNLRb84b9x3W412PKEY8KwI
tVthDBVFFpgLUCImfLhdcbdgfGW3kL9riE+Xot0f5WxxwowjljZJ2vMoil6qMgP2
38uxIv7dbFAuxhm8zy8Qq2ovasFVVFxXFME5l9vfyu6BEe1F+if3k9n1i2Db4gm+
OWRxVAT1AgMBAAECggEAJCdaL07UYY28KjPFRgLkbW7LwGLfL5jEr/4dqawgrlGL
LVcfZJwr9EGNpahho5aKXWP9oenLwl97ZYB421L/0Eyfahb1SZf5UNbzBIsnxrma
DU000jjpo8s4nPvI+h/GwkI1Qi4JdT2u+N2Sqro6OV62G33ABYuqQFfQXM4JJtKS
OpHg/OrM+mIfMWI2w9U9BhDldMSgDsEkskqVoq3V5S69tPKOKAk/0LU/gQ/mvDZr
2UTQkwpPuABOf/UaOd/RdtPGaAfPUFZp+BrRoGHdDKz/3LgCk8ta2IHRsCVT/hkN
q0rd8uR4DS+Tv9hhWCb8tMUUvObTtn7xfrXi0J1zEQKBgQD2Q6Ega0VfELuILP+T
0m+bnPJwBS5I4HDaQKF0SqJBwzonfbysQFtSLh2aC+yKg43n6fseWRBt/RrCpqMG
CyAXN2Bad1AGVlq7UvbTQAwmL/a0g6JBzBw7pBRDnRerCi6MkMM9kjgshhEBUwBt
gR0gXQPvqebM+flr3uy/beqd2QKBgQDMdtkZkGEKraXkrNPjogOB/dkrxZyqifin
Us8PychvG0pR+r9UvB6IjytO5ENbZba9oQKYxYzRgATphQDa0Cdr+fX2znGEEcx1
4hsFsrZmdpwM4OWoJqKuJh5gjj54fvTllNshamVUCtcChIEFLpcztGhri1oZ5RqG
WtLCTstCfQKBgGLyMV8xOqJDutWwHtBqEOXZXZ+ctUgaxb0rkrOC+UarBkavwosD
IK4YUVR/zf6pdO7lmDxNVMiclY/4HDBlb/NkEJbIsaKaN8JkR0ABz9/YDavh5+O/
+ugLuQihqczTPnjEAW5PvbpF87cwfk+BPQ6v0NEO1uks1wZ8f2s64rWhAoGADzS/
jKEqNmsXrVwVHhbMf/xrDFBUCHLeZUNJVvlAyNKUwmgNlrWu56UOKX3cbI2x+4Rf
9xCqGmKU+vEDUUKty9/5JPPRiWrxc8rvC4tw+UnU7ThEnC7TZnQ7Sh8KElgOyow5
h9Cr3IY521wTZFlEsXm3Ulg4yTg6ssdKaV0GzNkCgYBF3yge2KIU2bPgVYauY8Au
rwAJIToGnlGINdjRvJfTtnsv+pAn+NRDNjQA4KoUxxxROmD0hTIQQYJyiKC0bzD2
kIdYNPZpO67dDz+obwFyIlS8ZwR5WhiGS5IM0uu5qjMfjJvRoouJcjFsjVyjBYsu
0c7v/diAeg/XB4wvU52pdg==
-----END PRIVATE KEY-----";
}

function getProteanPublicKey()
{
    return "-----BEGIN PUBLIC KEY-----
MIIBITANBgkqhkiG9w0BAQEFAAOCAQ4AMIIBCQKCAQBw7Zq8McjphWnzjTN8T/0HukitNqWKSTIu6RWQP7OcuEuNQKTLE4Y5Cv+6gPoQslixD1KxHehJ7rqrm0lgGfL3DVv5ljzNSzp+mYHwRaBghplXqjasE2BrI5uHwNMXgaZXbL8UZbNUrTjsdsSjcFrI5XUhrsUPimlgO+4p2lh6w5vvlmSAZKCddOwCxvRrZ3IG7/aVPfftSTsLCU8LkeztcrqSwTTq3MrO46kRsW9vX/VJLr9VShfbdV1VHPPDXKIhut2jlNmDpXWczssWQ311h13+ZAVs9uKH/O7t88hwloSZDI77avbF2X4HmRYgRDfXBDe7JW6c0eeF8S8AGCSZAgMBAAE=
-----END PUBLIC KEY-----";
}

function getServerPublicKeyFromPrivate($privateKey)
{
    $rsa = new RSA();
    $rsa->loadKey($privateKey);
    return $rsa->getPublicKey();
}

// Get keys
$privateKeyString = getPrivateKey();
$publicKeyString = getProteanPublicKey();
$serverPublicKey = getServerPublicKeyFromPrivate($privateKeyString);

// Key status for display
$keyStatus = [
    'protean_public_content' => !empty($publicKeyString),
    'private_content' => !empty($privateKeyString),
    'server_public_content' => !empty($serverPublicKey)
];

// Process form submission
$result = '';
$error = '';
$accessToken = '';
$apiResponse = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if this is a key regeneration request
    if (isset($_POST['regenerate_keys'])) {
        $genResult = generateKeyPair();
        if ($genResult['success']) {
            $privateKeyString = $genResult['private_key'];
            $serverPublicKey = $genResult['public_key'];
            $keyStatus['private_exists'] = true;
            $keyStatus['private_content'] = true;
            $keyStatus['server_public_exists'] = true;
            $result .= "<div class='success'>✓ " . $genResult['message'] . "</div>";
            // Refresh the page to show new keys
            echo "<meta http-equiv='refresh' content='2'>";
        } else {
            $error = $genResult['message'];
        }
    } else {
        $selectedApi = $_POST['selected_api'] ?? 'epfo_employee_search';
        try {
            // Mobile API Flow: First get OAuth Access Token
            $apiKey = DEFAULT_API_KEY;
            $secretKey = DEFAULT_SECRET_KEY;

            $result .= "<div class='step'><h3>Step 1: Getting OAuth Token</h3>";
            $tokenData = getOAuthToken($apiKey, $secretKey);

            if (isset($tokenData['access_token'])) {
                $accessToken = $tokenData['access_token'];
                $result .= "<div class='success'>✓ Token obtained successfully</div>";
                $result .= "<div class='details'><strong>Access Token:</strong> " . htmlspecialchars($accessToken) . "</div>";
            } else {
                throw new Exception("Failed to get access token: " . ($tokenData['Error'] ?? json_encode($tokenData)));
            }
            $result .= "</div>";

            // Step 2: Prepare the plain request body based on the selected API
            $plainRequest = [];
            $apiUrl = "";
            $apiName = "";

            switch ($selectedApi) {
                case 'epfo_employee_search':
                    $plainRequest = [
                        'establishmentId' => $_POST['establishment_id'] ?? '',
                        'establishmentName' => $_POST['establishment_name'] ?? '',
                        'employeeName' => $_POST['employee_name'] ?? '',
                        'employmentMonth' => $_POST['employment_month'] ?? ''
                    ];
                    $apiUrl = API_BASE_URL . EPFO_EMPLOYEE_NAME_SEARCH_URL;
                    $apiName = "EPFO Employee Name Search";
                    break;

                case 'epf_uan_lookup':
                    $plainRequest = [
                        'consent' => $_POST['consent'] ?? 'Y',
                        'mobile' => $_POST['uan_mobile'] ?? '',
                        'clientData' => [
                            'caseId' => $_POST['case_id'] ?? ''
                        ]
                    ];
                    $apiUrl = API_BASE_URL . EPF_UAN_LOOKUP_URL;
                    $apiName = "EPF UAN Lookup";
                    break;

                case 'passport_verification':
                    $plainRequest = [
                        'fileNumber' => $_POST['file_number'] ?? '',
                        'dob' => $_POST['passport_dob'] ?? ''
                    ];
                    $apiUrl = API_BASE_URL . PASSPORT_VERIFICATION_URL;
                    $apiName = "Passport Verification";
                    break;

                case 'voter_id_verification':
                    $plainRequest = [
                        'epicNumber' => $_POST['epic_number'] ?? '',
                        'getAdditionalData' => $_POST['get_additional_data'] ?? 'true'
                    ];
                    $apiUrl = API_BASE_URL . VOTER_ID_VERIFICATION_URL;
                    $apiName = "Voter ID Verification";
                    break;

                case 'dl_verification':
                    $plainRequest = [
                        'number' => $_POST['dl_number'] ?? '',
                        'dob' => $_POST['dl_dob'] ?? ''
                    ];
                    $apiUrl = API_BASE_URL . DL_VERIFICATION_URL;
                    $apiName = "Drivers License Verification";
                    break;

                case 'vehicle_rc':
                    $plainRequest = [
                        'vehicleNumber' => $_POST['vehicle_number'] ?? ''
                    ];
                    $apiUrl = API_BASE_URL . VEHICLE_RC_URL;
                    $apiName = "Vehicle RC Authentication";
                    break;

                case 'vehicle_reverse_rc':
                    $plainRequest = [
                        'engineNumber' => $_POST['engine_number'] ?? '',
                        'chassisNumber' => $_POST['chassis_number'] ?? ''
                    ];
                    $apiUrl = API_BASE_URL . VEHICLE_REVERSE_RC_URL;
                    $apiName = "Vehicle Reverse RC";
                    break;

                case 'fast_tag':
                    $plainRequest = [
                        'vehicleNumber' => $_POST['fastag_vehicle_number'] ?? ''
                    ];
                    $apiUrl = API_BASE_URL . FAST_TAG_URL;
                    $apiName = "Fast Tag Verification";
                    break;

                case 'fasttag_last_location':
                    $plainRequest = [
                        'vehicleNumber' => $_POST['fastag_location_vehicle_number'] ?? ''
                    ];
                    $apiUrl = API_BASE_URL . FASTTAG_LAST_LOCATION_URL;
                    $apiName = "FASTTAG Last Location Verification";
                    break;

                case 'electricity_bill':
                    $plainRequest = [
                        'electricityProvider' => $_POST['electricity_provider'] ?? '',
                        'consumerNo' => $_POST['consumer_no'] ?? '',
                        'mobileNo' => $_POST['electricity_mobile'] ?? ''
                    ];
                    $apiUrl = API_BASE_URL . ELECTRICITY_BILL_URL;
                    $apiName = "Electricity Bill Authentication";
                    break;
            }

            $result .= "<div class='step'><h3>Step 2: Preparing Request Body for $apiName</h3>";
            $result .= "<div class='details'><strong>Plain Request Body:</strong><br><pre>" . htmlspecialchars(json_encode($plainRequest, JSON_PRETTY_PRINT)) . "</pre></div>";

            // Step 3: Create encrypted request payload
            $result .= "<div class='step'><h3>Step 3: Creating Encrypted Request Payload</h3>";

            // Generate symmetric key
            $symmetricKey = '1234567890123456'; // Use fixed key as in current implementation
            $result .= "<div class='details'><strong>Symmetric Key (Base64):</strong> " . base64_encode($symmetricKey) . "</div>";

            // Encrypt symmetric key with RSA public key
            $encryptedKey = encryptRSA($symmetricKey, $publicKeyString);
            if (!$encryptedKey) throw new Exception("RSA encryption failed. Check if public key is valid.");
            $result .= "<div class='details'><strong>Encrypted Symmetric Key (Base64):</strong> " . $encryptedKey . "</div>";

            // Encrypt the actual data with AES-256-GCM
            $plainTextJson = json_encode($plainRequest);
            $encryptedData = encrypt($plainTextJson, $symmetricKey);
            $result .= "<div class='details'><strong>Encrypted Data (Base64):</strong> " . $encryptedData . "</div>";

            // Calculate HMAC
            $hmac = calculateHmacSHA256($symmetricKey, $plainTextJson);
            $result .= "<div class='details'><strong>HMAC:</strong> " . $hmac . "</div>";

            // Create final payload
            $encryptedRequest = [
                'data' => $encryptedData,
                'version' => API_VERSION,
                'symmetricKey' => $encryptedKey,
                'hash' => $hmac,
                'timestamp' => date('Y-m-d\TH:i:s.v'),
                'requestId' => generateUUID()
            ];

            $result .= "<div class='details'><strong>Final Request Payload:</strong><br><pre>" . htmlspecialchars(json_encode($encryptedRequest, JSON_PRETTY_PRINT)) . "</pre></div>";
            $result .= "</div>";

            // Step 4: Send encrypted request to API
            $result .= "<div class='step'><h3>Step 4: Sending Encrypted Request to $apiName</h3>";

            // Unified API call function
            $callResponse = (function ($url, $accessToken, $apiKey, $payload) {
                $jsonData = json_encode($payload);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $accessToken,
                    'apikey: ' . $apiKey,
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonData)
                ]);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);

                if ($curlError) return ['http_code' => 500, 'response_data' => ['error' => 'CURL Error: ' . $curlError]];
                return ['http_code' => $httpCode, 'response_data' => json_decode($response, true) ?: ['raw_response' => $response]];
            })($apiUrl, $accessToken, $apiKey, $encryptedRequest);

            // Step 5: Handle and Render Response
            $result .= renderApiResponse($callResponse, $privateKeyString, $keyStatus);
            $result .= "</div>";
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Updated encryption/decryption functions matching the first script

function getRandomBytes($length)
{
    return openssl_random_pseudo_bytes($length);
}

function getAESKeyFromPassword($password, $salt)
{
    return openssl_pbkdf2($password, $salt, 32, 65536, 'sha256');
}

function encrypt($plainText, $plainSymmetricKey)
{
    $salt = getRandomBytes(16);
    $iv = getRandomBytes(12);
    $aesKeyFromPassword = getAESKeyFromPassword($plainSymmetricKey, $salt);

    $tag = '';
    $cipherText = openssl_encrypt($plainText, 'aes-256-gcm', $aesKeyFromPassword, OPENSSL_RAW_DATA, $iv, $tag);

    // Note: Order is IV + Salt + CipherText + Tag
    $cipherTextWithIvSalt = $iv . $salt . $cipherText . $tag;

    return base64_encode($cipherTextWithIvSalt);
}

function decrypt($encodedData, $decryptedKey)
{
    $decodedData = base64_decode($encodedData);

    // Extract in correct order: IV (12) + Salt (16) + Ciphertext + Tag (16)
    $iv = substr($decodedData, 0, 12);
    $salt = substr($decodedData, 12, 16);
    $tag = substr($decodedData, -16);
    $ciphertext = substr($decodedData, 28, -16);

    // Derive AES key using PBKDF2
    $aesKey = openssl_pbkdf2($decryptedKey, $salt, 32, 65536, 'sha256');

    // Decrypt using AES-256-GCM
    $plaintext = openssl_decrypt($ciphertext, 'aes-256-gcm', $aesKey, OPENSSL_RAW_DATA, $iv, $tag);

    return $plaintext;
}

function calculateHmacSHA256($plainSymmetricKeyReceived, $data)
{
    $hash = hash_hmac('sha256', $data, $plainSymmetricKeyReceived, true);
    return base64_encode($hash);
}

function encryptRSA($data, $rsaPublicKey)
{
    if (empty($rsaPublicKey)) {
        return false;
    }

    try {
        $rsa = new RSA();
        $rsa->setMGFHash('sha256');
        $rsa->setHash('sha256');
        $rsa->loadKey($rsaPublicKey);
        $encrypted = $rsa->encrypt($data);

        if ($encrypted === false) {
            return false;
        }

        return base64_encode($encrypted);
    } catch (Exception $e) {
        error_log("RSA Encryption Error: " . $e->getMessage());
        return false;
    }
}

function decryptRSA($data, $rsaPrivateKey)
{
    $rsa = new RSA();
    $rsa->setMGFHash('sha256');
    $rsa->setHash('sha256');
    $rsa->loadKey($rsaPrivateKey);

    $data = base64_decode($data);
    $decrypted = $rsa->decrypt($data);

    if ($decrypted === false) {
        return false;
    }

    return $decrypted;
}

function getOAuthToken($apiKey, $secretKey)
{
    $url = API_BASE_URL . OAUTH_TOKEN_URL;
    $auth = base64_encode($apiKey . ':' . $secretKey);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['grant_type' => 'client_credentials']));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $auth,
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['error' => 'CURL Error: ' . $curlError];
    }

    $decodedResponse = json_decode($response, true);

    if ($httpCode !== 200) {
        return ['error' => 'HTTP Error: ' . $httpCode, 'details' => $response];
    }

    return $decodedResponse;
}

function decryptErrorResponse($errorResponse, $privateKey)
{
    try {
        if (!isset($errorResponse['symmetricKey']) || !isset($errorResponse['data']) || !isset($errorResponse['hash'])) {
            return ['error' => 'Response does not contain encrypted data fields', 'raw' => $errorResponse];
        }

        // Diagnose key size mismatch before attempting decryption
        $encSymKeyBytes = strlen(base64_decode($errorResponse['symmetricKey']));
        $privKeyInfo = openssl_pkey_get_private($privateKey);
        $ourKeyBits = $privKeyInfo ? openssl_pkey_get_details($privKeyInfo)['bits'] : 0;
        $expectedBytes = $ourKeyBits / 8;

        if ($encSymKeyBytes !== $expectedBytes && $encSymKeyBytes > 0) {
            return [
                'error' => 'RSA key size mismatch',
                'detail' => sprintf(
                    'Response symmetric key is %d bytes (RSA-%d), but our private key is RSA-%d (%d bytes). '
                        . 'Protean is NOT using our server public key to encrypt responses. '
                        . 'You must share your server public key with Protean and ask them to register it.',
                    $encSymKeyBytes,
                    $encSymKeyBytes * 8,
                    $ourKeyBits,
                    $expectedBytes
                )
            ];
        }

        $decryptedKey = decryptRSA($errorResponse['symmetricKey'], $privateKey);
        if ($decryptedKey === false) {
            return ['error' => 'Failed to decrypt symmetric key. Check if private key is correct.'];
        }

        $decryptedData = decrypt($errorResponse['data'], $decryptedKey);
        if ($decryptedData === false) {
            return ['error' => 'Failed to decrypt error data using AES'];
        }

        $calculatedHmac = calculateHmacSHA256($decryptedKey, $decryptedData);
        if ($calculatedHmac !== $errorResponse['hash']) {
            return [
                'error' => 'HMAC verification failed',
                'decrypted_data' => $decryptedData,
                'expected_hmac' => $errorResponse['hash'],
                'calculated_hmac' => $calculatedHmac
            ];
        }

        $decryptedJson = json_decode($decryptedData, true);
        if ($decryptedJson === null) {
            return [
                'error' => 'Failed to parse decrypted data as JSON',
                'raw_data' => $decryptedData
            ];
        }

        return $decryptedJson;
    } catch (Exception $e) {
        return ['error' => 'Exception: ' . $e->getMessage()];
    }
}

function decryptSuccessResponse($response, $privateKey)
{
    try {
        if (!isset($response['symmetricKey']) || !isset($response['data']) || !isset($response['hash'])) {
            return ['error' => 'Response does not contain encrypted data fields'];
        }

        // Diagnose key size mismatch before attempting decryption
        $encSymKeyBytes = strlen(base64_decode($response['symmetricKey']));
        $privKeyInfo = openssl_pkey_get_private($privateKey);
        $ourKeyBits = $privKeyInfo ? openssl_pkey_get_details($privKeyInfo)['bits'] : 0;
        $expectedBytes = $ourKeyBits / 8;

        if ($encSymKeyBytes !== $expectedBytes && $encSymKeyBytes > 0) {
            return [
                'error' => 'RSA key size mismatch',
                'detail' => sprintf(
                    'Response symmetric key is %d bytes (RSA-%d), but our private key is RSA-%d (%d bytes). '
                        . 'Protean is NOT using our server public key to encrypt responses. '
                        . 'You must share your server public key with Protean and ask them to register it.',
                    $encSymKeyBytes,
                    $encSymKeyBytes * 8,
                    $ourKeyBits,
                    $expectedBytes
                )
            ];
        }

        // Decrypt the symmetric key from the response using your private key
        $decryptedKey = decryptRSA($response['symmetricKey'], $privateKey);
        if ($decryptedKey === false) {
            return ['error' => 'Failed to decrypt symmetric key. Check if your private key matches what you shared with Protean.'];
        }

        $decryptedData = decrypt($response['data'], $decryptedKey);
        if ($decryptedData === false) {
            return ['error' => 'Failed to decrypt response data'];
        }

        $calculatedHmac = calculateHmacSHA256($decryptedKey, $decryptedData);
        if ($calculatedHmac !== $response['hash']) {
            return [
                'error' => 'HMAC verification failed',
                'expected' => $response['hash'],
                'calculated' => $calculatedHmac
            ];
        }

        $decryptedJson = json_decode($decryptedData, true);
        if ($decryptedJson === null) {
            return ['error' => 'Failed to parse decrypted data as JSON', 'raw_data' => $decryptedData];
        }

        return $decryptedJson;
    } catch (Exception $e) {
        return ['error' => 'Exception: ' . $e->getMessage()];
    }
}

/**
 * Renders the API response with decryption support
 */
function renderApiResponse($apiResponse, $privateKeyString, $keyStatus = [])
{
    $result = "";
    if ($apiResponse['http_code'] >= 400) {
        $result .= "<div class='error'>✗ API Error: HTTP " . $apiResponse['http_code'] . "</div>";

        // Check if we have a valid response to decrypt
        if (!empty($apiResponse['response_data']) && is_array($apiResponse['response_data'])) {
            // Check if it's an encrypted response
            if (isset($apiResponse['response_data']['data']) && isset($apiResponse['response_data']['symmetricKey'])) {
                $result .= "<div class='info'>🔓 Attempting to decrypt error response...</div>";

                if (empty($privateKeyString)) {
                    $result .= "<div class='error-details'>";
                    $result .= "<strong>❌ Private Key Error:</strong><br>";
                    $result .= "Private key is not available or not loaded properly.<br>";
                    if (!empty($keyStatus)) {
                        $result .= "<strong>Key Status:</strong><br>";
                        $result .= "- Protean Public key: " . ($keyStatus['protean_public_content'] ? '✓ Loaded' : '✗ Missing') . "<br>";
                        $result .= "- Server Private key: " . ($keyStatus['private_content'] ? '✓ Loaded' : '✗ Missing') . "<br>";
                    }
                    $result .= "</div>";
                    $result .= "<div class='details'><strong>Raw Error Response:</strong><br><pre>" . htmlspecialchars(json_encode($apiResponse['response_data'], JSON_PRETTY_PRINT)) . "</pre></div>";
                } else {
                    $decryptedError = decryptErrorResponse($apiResponse['response_data'], $privateKeyString);
                    if ($decryptedError && !isset($decryptedError['error'])) {
                        $result .= "<div class='success'>✓ Error response decrypted successfully</div>";
                        $result .= "<div class='error-details'><strong>📋 Decrypted Error Details:</strong><br><pre>" . htmlspecialchars(json_encode($decryptedError, JSON_PRETTY_PRINT)) . "</pre></div>";
                    } else {
                        $errorMsg = isset($decryptedError['error']) ? (is_scalar($decryptedError['error']) ? $decryptedError['error'] : json_encode($decryptedError['error'])) : 'Unknown error';
                        $result .= "<div class='error'><strong>❌ " . htmlspecialchars((string)$errorMsg) . "</strong></div>";
                        if (!empty($decryptedError['detail'])) {
                            $result .= "<div class='error-details'><strong>🔍 Diagnosis:</strong><br>" . htmlspecialchars($decryptedError['detail']) . "</div>";
                        }
                        $result .= "<div class='details'><strong>Raw Error Response:</strong><br><pre>" . htmlspecialchars(json_encode($apiResponse['response_data'], JSON_PRETTY_PRINT)) . "</pre></div>";
                    }
                }
            } else {
                $result .= "<div class='details'><strong>Response:</strong><br><pre>" . htmlspecialchars(json_encode($apiResponse['response_data'], JSON_PRETTY_PRINT)) . "</pre></div>";
            }
        } else {
            $result .= "<div class='error'>Invalid response format from API</div>";
            $result .= "<div class='details'>Response: " . htmlspecialchars(print_r($apiResponse, true)) . "</div>";
        }
    } else {
        $result .= "<div class='success'>✓ API call successful</div>";

        // Decrypt successful response if encrypted
        if (isset($apiResponse['response_data']['symmetricKey']) && isset($apiResponse['response_data']['data'])) {
            $result .= "<div class='info'>🔓 Decrypting response...</div>";
            $decryptedResponse = decryptSuccessResponse($apiResponse['response_data'], $privateKeyString);
            if ($decryptedResponse && !isset($decryptedResponse['error'])) {
                $result .= "<div class='response'><strong>Decrypted Response:</strong><br><pre>" . htmlspecialchars(json_encode($decryptedResponse, JSON_PRETTY_PRINT)) . "</pre></div>";
            } else {
                $result .= "<div class='error'>Failed to decrypt response: " . ($decryptedResponse['error'] ?? 'Unknown error') . "</div>";
                $result .= "<div class='details'><strong>Raw Response:</strong><br><pre>" . htmlspecialchars(json_encode($apiResponse['response_data'], JSON_PRETTY_PRINT)) . "</pre></div>";
            }
        } else {
            $result .= "<div class='response'><strong>Response:</strong><br><pre>" . htmlspecialchars(json_encode($apiResponse['response_data'], JSON_PRETTY_PRINT)) . "</pre></div>";
        }
    }
    return $result;
}

function generateUUID()
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protean API Tester</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
            color: white;
            padding: 20px 30px;
        }

        .card-header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .card-header p {
            margin: 5px 0 0;
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .card-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #4a5568;
        }

        input,
        select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .result {
            margin-top: 30px;
            border-top: 2px solid #e2e8f0;
            padding-top: 20px;
        }

        .step {
            background: #f7fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .step h3 {
            margin: 0 0 15px 0;
            color: #4a5568;
        }

        .success {
            color: #38a169;
            background: #f0fff4;
            padding: 10px;
            border-radius: 8px;
            margin: 10px 0;
        }

        .error {
            color: #e53e3e;
            background: #fff5f5;
            padding: 10px;
            border-radius: 8px;
            margin: 10px 0;
        }

        .details {
            background: #edf2f7;
            padding: 10px;
            border-radius: 8px;
            margin: 10px 0;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
        }

        .response {
            background: #1a202c;
            color: #68d391;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            overflow-x: auto;
        }

        .response pre {
            margin: 0;
            font-size: 12px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .row {
            display: flex;
            gap: 20px;
        }

        .row .form-group {
            flex: 1;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .row {
                flex-direction: column;
                gap: 0;
            }

            .card-body {
                padding: 20px;
            }

            .button-group {
                flex-direction: column;
            }
        }

        .api-selector-wrap {
            margin-bottom: 28px;
        }

        .api-selector-wrap label {
            font-size: 1rem;
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 8px;
            display: block;
        }

        .api-selector-wrap select {
            font-size: 15px;
            background: #f7fafc;
            border: 2px solid #667eea;
            color: #2d3748;
            font-weight: 600;
        }

        .api-fields {
            display: none;
        }

        .api-fields.active {
            display: block;
        }

        .api-fields:first-of-type {
            display: block;
        }

        .badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            margin-left: 8px;
            vertical-align: middle;
        }

        .badge-rest {
            background: #f0fff4;
            color: #276749;
        }

        .api-desc {
            font-size: 13px;
            color: #718096;
            background: #f7fafc;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 18px;
            border-left: 3px solid #667eea;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>🔐 Protean API Tester</h1>
                <p>Select an API from the dropdown below to test it</p>
            </div>
            <div class="card-body">

                <form method="POST" action="" id="apiForm">

                    <!-- API Selector -->
                    <div class="api-selector-wrap">
                        <label>🌐 Select API to Test</label>
                        <select name="selected_api" id="selectedApi" onchange="switchApi(this.value)">
                            <option value="epfo_employee_search" <?php echo (($_POST['selected_api'] ?? '') === 'epfo_employee_search' || ($_POST['selected_api'] ?? '') === '') ? 'selected' : ''; ?>>1. EPFO Employee Name Search</option>
                            <option value="epf_uan_lookup" <?php echo (($_POST['selected_api'] ?? '') === 'epf_uan_lookup') ? 'selected' : ''; ?>>2. EPF UAN Lookup</option>
                            <option value="passport_verification" <?php echo (($_POST['selected_api'] ?? '') === 'passport_verification') ? 'selected' : ''; ?>>3. Passport Verification</option>
                            <option value="voter_id_verification" <?php echo (($_POST['selected_api'] ?? '') === 'voter_id_verification') ? 'selected' : ''; ?>>4. Voter ID Verification</option>
                            <option value="dl_verification" <?php echo (($_POST['selected_api'] ?? '') === 'dl_verification') ? 'selected' : ''; ?>>5. Drivers License Verification</option>
                            <option value="vehicle_rc" <?php echo (($_POST['selected_api'] ?? '') === 'vehicle_rc') ? 'selected' : ''; ?>>6. Vehicle RC Authentication</option>
                            <option value="vehicle_reverse_rc" <?php echo (($_POST['selected_api'] ?? '') === 'vehicle_reverse_rc') ? 'selected' : ''; ?>>7. Vehicle Reverse RC</option>
                            <option value="fast_tag" <?php echo (($_POST['selected_api'] ?? '') === 'fast_tag') ? 'selected' : ''; ?>>8. Fast Tag Verification</option>
                            <option value="fasttag_last_location" <?php echo (($_POST['selected_api'] ?? '') === 'fasttag_last_location') ? 'selected' : ''; ?>>9. FASTTAG Last Location Verification</option>
                            <option value="electricity_bill" <?php echo (($_POST['selected_api'] ?? '') === 'electricity_bill') ? 'selected' : ''; ?>>10. Electricity Bill Authentication</option>
                        </select>
                    </div>

                    <!-- API 1: EPFO Employee Name Search -->
                    <div class="api-fields" id="fields_epfo_employee_search">
                        <div class="api-desc">💼 <strong>EPFO Employee Name Search</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/epfo/employee-name-search</code></div>
                        <div class="row">
                            <div class="form-group">
                                <label>🏢 Establishment ID <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="establishment_id" value="<?php echo htmlspecialchars($_POST['establishment_id'] ?? ''); ?>" placeholder="e.g. PXXXXXXXXXXXXXX">
                            </div>
                            <div class="form-group">
                                <label>🏢 Establishment Name <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="establishment_name" value="<?php echo htmlspecialchars($_POST['establishment_name'] ?? ''); ?>" placeholder="e.g. ABC Technologies Private Limited">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label>👤 Employee Name <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="employee_name" value="<?php echo htmlspecialchars($_POST['employee_name'] ?? ''); ?>" placeholder="e.g. John Doe">
                            </div>
                            <div class="form-group">
                                <label>📅 Employment Month <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="employment_month" value="<?php echo htmlspecialchars($_POST['employment_month'] ?? ''); ?>" placeholder="e.g. 02/XXXX">
                            </div>
                        </div>
                    </div>

                    <!-- API 2: EPF UAN Lookup -->
                    <div class="api-fields" id="fields_epf_uan_lookup">
                        <div class="api-desc">🔍 <strong>EPF UAN Lookup</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/epfes/uan-lookup</code></div>
                        <div class="row">
                            <div class="form-group" style="max-width:150px">
                                <label>✅ Consent <span style="color:#e53e3e">*</span></label>
                                <select name="consent">
                                    <option value="Y" <?php echo (($_POST['consent'] ?? 'Y') == 'Y') ? 'selected' : ''; ?>>Y</option>
                                    <option value="N" <?php echo (($_POST['consent'] ?? 'Y') == 'N') ? 'selected' : ''; ?>>N</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>📱 Mobile Number <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="uan_mobile" value="<?php echo htmlspecialchars($_POST['uan_mobile'] ?? ''); ?>" placeholder="e.g. 954587XXXX">
                            </div>
                            <div class="form-group">
                                <label>📋 Case ID <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="case_id" value="<?php echo htmlspecialchars($_POST['case_id'] ?? ''); ?>" placeholder="e.g. 123456">
                            </div>
                        </div>
                    </div>

                    <!-- API 3: Passport Verification -->
                    <div class="api-fields" id="fields_passport_verification">
                        <div class="api-desc">🛂 <strong>Passport Verification</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/passport-verification</code></div>
                        <div class="row">
                            <div class="form-group">
                                <label>📄 File Number <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="file_number" value="<?php echo htmlspecialchars($_POST['file_number'] ?? ''); ?>" placeholder="e.g. XXXXXXXXXXXXXXX">
                            </div>
                            <div class="form-group">
                                <label>🎂 Date of Birth <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="passport_dob" value="<?php echo htmlspecialchars($_POST['passport_dob'] ?? ''); ?>" placeholder="e.g. 17/08/XXXX">
                            </div>
                        </div>
                    </div>

                    <!-- API 4: Voter ID Verification -->
                    <div class="api-fields" id="fields_voter_id_verification">
                        <div class="api-desc">🗳️ <strong>Voter ID Verification</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/voter-id-verification</code></div>
                        <div class="row">
                            <div class="form-group">
                                <label>🆔 EPIC Number <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="epic_number" value="<?php echo htmlspecialchars($_POST['epic_number'] ?? ''); ?>" placeholder="e.g. AISXXXXXXX">
                            </div>
                            <div class="form-group" style="max-width:200px">
                                <label>📊 Additional Data</label>
                                <select name="get_additional_data">
                                    <option value="true" <?php echo (($_POST['get_additional_data'] ?? 'true') == 'true') ? 'selected' : ''; ?>>True</option>
                                    <option value="false" <?php echo (($_POST['get_additional_data'] ?? 'true') == 'false') ? 'selected' : ''; ?>>False</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- API 5: Drivers License Verification -->
                    <div class="api-fields" id="fields_dl_verification">
                        <div class="api-desc">🚗 <strong>Drivers License Verification</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/dl-verification</code></div>
                        <div class="row">
                            <div class="form-group">
                                <label>🔢 License Number <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="dl_number" value="<?php echo htmlspecialchars($_POST['dl_number'] ?? ''); ?>" placeholder="e.g. MH0120130001960">
                            </div>
                            <div class="form-group">
                                <label>🎂 Date of Birth <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="dl_dob" value="<?php echo htmlspecialchars($_POST['dl_dob'] ?? ''); ?>" placeholder="e.g. 05-10-1994">
                            </div>
                        </div>
                    </div>

                    <!-- API 6: Vehicle RC Authentication -->
                    <div class="api-fields" id="fields_vehicle_rc">
                        <div class="api-desc">🚙 <strong>Vehicle RC Authentication</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/vehicle-detailed-advanced</code></div>
                        <div class="form-group">
                            <label>🚗 Vehicle Number <span style="color:#e53e3e">*</span></label>
                            <input type="text" name="vehicle_number" value="<?php echo htmlspecialchars($_POST['vehicle_number'] ?? ''); ?>" placeholder="e.g. MH19XXXXXX">
                        </div>
                    </div>

                    <!-- API 7: Vehicle Reverse RC -->
                    <div class="api-fields" id="fields_vehicle_reverse_rc">
                        <div class="api-desc">🔄 <strong>Vehicle Reverse RC</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/vehicle/reverse-search</code></div>
                        <div class="row">
                            <div class="form-group">
                                <label>⚙️ Engine Number <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="engine_number" value="<?php echo htmlspecialchars($_POST['engine_number'] ?? ''); ?>" placeholder="e.g. XXXXXXXXXXX">
                            </div>
                            <div class="form-group">
                                <label>🔧 Chassis Number <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="chassis_number" value="<?php echo htmlspecialchars($_POST['chassis_number'] ?? ''); ?>" placeholder="e.g. XXXXXXXXXXXXX">
                            </div>
                        </div>
                    </div>

                    <!-- API 8: Fast Tag Verification -->
                    <div class="api-fields" id="fields_fast_tag">
                        <div class="api-desc">🏷️ <strong>Fast Tag Verification</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/vehicle/rc-to-fastag-details</code></div>
                        <div class="form-group">
                            <label>🚗 Vehicle Number <span style="color:#e53e3e">*</span></label>
                            <input type="text" name="fastag_vehicle_number" value="<?php echo htmlspecialchars($_POST['fastag_vehicle_number'] ?? ''); ?>" placeholder="e.g. PBXXXXXXXX">
                        </div>
                    </div>

                    <!-- API 9: FASTTAG Last Location Verification -->
                    <div class="api-fields" id="fields_fasttag_last_location">
                        <div class="api-desc">📍 <strong>FASTTAG Last Location Verification</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/vehicle/fastag-history-details</code></div>
                        <div class="form-group">
                            <label>🚗 Vehicle Number <span style="color:#e53e3e">*</span></label>
                            <input type="text" name="fastag_location_vehicle_number" value="<?php echo htmlspecialchars($_POST['fastag_location_vehicle_number'] ?? ''); ?>" placeholder="e.g. XXXXXXXXXX">
                        </div>
                    </div>

                    <!-- API 10: Electricity Bill Authentication -->
                    <div class="api-fields" id="fields_electricity_bill">
                        <div class="api-desc">⚡ <strong>Electricity Bill Authentication</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/electricity-bill-authe</code></div>
                        <div class="row">
                            <div class="form-group">
                                <label>🏭 Electricity Provider <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="electricity_provider" value="<?php echo htmlspecialchars($_POST['electricity_provider'] ?? ''); ?>" placeholder="e.g. MAHAVITRAN">
                            </div>
                            <div class="form-group">
                                <label>🔢 Consumer Number <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="consumer_no" value="<?php echo htmlspecialchars($_POST['consumer_no'] ?? ''); ?>" placeholder="e.g. XXXXXXXXXXXX">
                            </div>
                            <div class="form-group">
                                <label>📱 Mobile Number <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="electricity_mobile" value="<?php echo htmlspecialchars($_POST['electricity_mobile'] ?? ''); ?>" placeholder="e.g. XXXXXXXXXX">
                            </div>
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="submit" id="submitBtn">🚀 Send Request</button>
                    </div>
                </form>

                <?php if ($error): ?>
                    <div class="result">
                        <div class="error">❌ Error: <?php echo htmlspecialchars($error); ?></div>
                    </div>
                <?php endif; ?>

                <?php if ($result): ?>
                    <div class="result">
                        <h2>📊 Execution Results</h2>
                        <?php echo $result; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function switchApi(val, isInitialLoad = false) {
            if (!isInitialLoad) {
                document.querySelectorAll('.result').forEach(el => el.style.display = 'none');
            }

            document.querySelectorAll('.api-fields').forEach(el => el.classList.remove('active'));
            const target = document.getElementById('fields_' + val);
            if (target) target.classList.add('active');

            const labels = {
                epfo_employee_search: '💼 Search EPFO Employee',
                epf_uan_lookup: '🔍 Lookup EPF UAN',
                passport_verification: '🛂 Verify Passport',
                voter_id_verification: '🗳️ Verify Voter ID',
                dl_verification: '🚗 Verify Drivers License',
                vehicle_rc: '🚙 Verify Vehicle RC',
                vehicle_reverse_rc: '🔄 Reverse Vehicle RC',
                fast_tag: '🏷️ Verify Fast Tag',
                fasttag_last_location: '📍 Get FASTTAG Location',
                electricity_bill: '⚡ Authenticate Electricity Bill'
            };
            document.getElementById('submitBtn').textContent = labels[val] || '🚀 Send Request';
        }

        (function init() {
            const sel = document.getElementById('selectedApi');
            if (sel) switchApi(sel.value, true);
        })();

        document.addEventListener('DOMContentLoaded', function() {
            const sel = document.getElementById('selectedApi');
            if (sel) switchApi(sel.value, true);
        });
    </script>
</body>

</html>