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
define('PHONE_KYC_URL', '/api/v1/protean/phones/phone-kyc-non-consent');
define('GENERATE_OTP_URL', '/api/v1/protean/phone/generateOtp');
define('GEO_FENCING_URL', '/api/v1/protean/patrons/riskscores');
define('REVERSE_GEOCODE_URL', '/api/v1/protean/geocoding/reverse-geocode');
define('KYC_OCR_URL', '/api/v1/protean/utility/single-kyc');
define('BANK_VERIFY_URL', '/api/v1/protean-variablepennydrop/bankaccountverifications/advancedverification');
define('BANK_VERIFY_AMOUNT_URL', '/api/v1/protean-variablepennydrop/bankaccountverification/verifytransferadvanced');
define('SHOP_ESTAB_URL', '/api/v1/protean/shop-establishment');
define('EPF_UAN_URL', '/api/v1/protean/fetch-employment-history');

// Default credentials
define('DEFAULT_API_KEY', 'VdMM80JNMwUG7A4Jn0n3dodE1Pk1pAXnPvP75zSYHZHaV8p6');
define('DEFAULT_SECRET_KEY', '6m7XMl3E9Fy6d8zWyb3CK564uei6C8eaUNOvyinEvIGre9advO0zsEEr9unnDT9a');
define('DEFAULT_MOBILE_NUMBER', '');
define('DEFAULT_ADDITIONAL_DETAILS', 'yes');
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
        $selectedApi = $_POST['selected_api'] ?? 'silent_verification';
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
                case 'generate_otp':
                    $plainRequest = ['countryCode' => $_POST['country_code'] ?? '91', 'mobileNumber' => $_POST['mobile_number'] ?? ''];
                    $apiUrl = API_BASE_URL . GENERATE_OTP_URL;
                    $apiName = "Generate OTP";
                    break;
                case 'geo_fencing':
                    $plainRequest = ['task' => $_POST['geo_task'] ?? 'geoFencing', 'essentials' => ['ip' => $_POST['geo_ip'] ?? '', 'country' => $_POST['geo_country'] ?? 'IN', 'state' => $_POST['geo_state'] ?? '']];
                    $apiUrl = API_BASE_URL . GEO_FENCING_URL;
                    $apiName = "Geo Fencing";
                    break;
                case 'reverse_geocode':
                    $plainRequest = ['latitude' => $_POST['latitude'] ?? '', 'longitude' => $_POST['longitude'] ?? ''];
                    $apiUrl = API_BASE_URL . REVERSE_GEOCODE_URL;
                    $apiName = "Reverse Geocode";
                    break;
                case 'kyc_ocr':
                    $kycUrls = array_values(array_filter(array_map('trim', $_POST['kyc_urls'] ?? []), 'strlen'));
                    if (empty($kycUrls)) throw new Exception('Please provide at least one image URL.');
                    $plainRequest = ['url' => $kycUrls];
                    $apiUrl = API_BASE_URL . KYC_OCR_URL;
                    $apiName = "KYC OCR Plus";
                    break;
                case 'bank_verify':
                    $plainRequest = ['beneficiaryAccount' => trim($_POST['beneficiary_account'] ?? ''), 'beneficiaryIFSC' => trim($_POST['beneficiary_ifsc'] ?? '')];
                    $apiUrl = API_BASE_URL . BANK_VERIFY_URL;
                    $apiName = "Bank Account Verification";
                    break;
                case 'bank_verify_amount':
                    $plainRequest = ['amount' => trim($_POST['verify_amount'] ?? ''), 'referenceid' => trim($_POST['verify_referenceid'] ?? '')];
                    $apiUrl = API_BASE_URL . BANK_VERIFY_AMOUNT_URL;
                    $apiName = "Bank Account Verification - Verify Amount";
                    break;
                case 'shop_estab':
                    $plainRequest = ['registrationNumber' => trim($_POST['reg_number'] ?? ''), 'state' => trim($_POST['se_state'] ?? '')];
                    $apiUrl = API_BASE_URL . SHOP_ESTAB_URL;
                    $apiName = "Shop and Establishment";
                    break;
                case 'epf_uan':
                    $plainRequest = ['uan' => trim($_POST['uan'] ?? '')];
                    $apiUrl = API_BASE_URL . EPF_UAN_URL;
                    $apiName = "EPF UAN Validation";
                    break;
                case 'silent_verification':
                default:
                    $plainRequest = ["mobileNumber" => $_POST['mobile_number'] ?? DEFAULT_MOBILE_NUMBER, "additionalDetails" => $_POST['additional_details'] ?? DEFAULT_ADDITIONAL_DETAILS];
                    $apiUrl = API_BASE_URL . PHONE_KYC_URL;
                    $apiName = "Mobile Silent Verification";
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

function callGenerateOtpAPI($apiKey, $bearerToken, $countryCode, $mobileNumber)
{
    $url = API_BASE_URL . GENERATE_OTP_URL;
    $payload = json_encode(['countryCode' => $countryCode, 'mobileNumber' => $mobileNumber]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . trim($bearerToken),
        'apikey: ' . $apiKey,
        'content-type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['http_code' => 500, 'response_data' => ['error' => 'CURL Error: ' . $curlError]];
    }

    $decoded = json_decode($response, true);
    return ['http_code' => $httpCode, 'response_data' => $decoded ?: ['raw_response' => $response]];
}

function callGeoFencingAPI($apiKey, $bearerToken, $task, $ip, $country, $state)
{
    $url = API_BASE_URL . GEO_FENCING_URL;
    $payload = json_encode([
        'task' => $task,
        'essentials' => [
            'ip'      => $ip,
            'country' => $country,
            'state'   => $state,
        ]
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . trim($bearerToken),
        'apikey: ' . $apiKey,
        'content-type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['http_code' => 500, 'response_data' => ['error' => 'CURL Error: ' . $curlError]];
    }

    $decoded = json_decode($response, true);
    return ['http_code' => $httpCode, 'response_data' => $decoded ?: ['raw_response' => $response]];
}

function callReverseGeocodeAPI($apiKey, $bearerToken, $latitude, $longitude)
{
    $url = API_BASE_URL . REVERSE_GEOCODE_URL;
    $payload = json_encode(['latitude' => $latitude, 'longitude' => $longitude]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . trim($bearerToken),
        'apikey: ' . $apiKey,
        'content-type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['http_code' => 500, 'response_data' => ['error' => 'CURL Error: ' . $curlError]];
    }

    $decoded = json_decode($response, true);
    return ['http_code' => $httpCode, 'response_data' => $decoded ?: ['raw_response' => $response]];
}

function callKycOcrAPI($apiKey, $bearerToken, array $urls)
{
    $url = API_BASE_URL . KYC_OCR_URL;
    $payload = json_encode(['url' => $urls]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . trim($bearerToken),
        'apikey: ' . $apiKey,
        'content-type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['http_code' => 500, 'response_data' => ['error' => 'CURL Error: ' . $curlError]];
    }

    $decoded = json_decode($response, true);
    return ['http_code' => $httpCode, 'response_data' => $decoded ?: ['raw_response' => $response]];
}

function callBankVerifyAPI($apiKey, $bearerToken, $benAccount, $benIFSC)
{
    $url = API_BASE_URL . BANK_VERIFY_URL;
    $payload = json_encode(['beneficiaryAccount' => $benAccount, 'beneficiaryIFSC' => $benIFSC]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . trim($bearerToken),
        'apikey: ' . $apiKey,
        'content-type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['http_code' => 500, 'response_data' => ['error' => 'CURL Error: ' . $curlError]];
    }
    $decoded = json_decode($response, true);
    return ['http_code' => $httpCode, 'response_data' => $decoded ?: ['raw_response' => $response]];
}

function callBankVerifyAmountAPI($apiKey, $bearerToken, $amount, $referenceId)
{
    $url = API_BASE_URL . BANK_VERIFY_AMOUNT_URL;
    $payload = json_encode(['amount' => $amount, 'referenceid' => $referenceId]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . trim($bearerToken),
        'apikey: ' . $apiKey,
        'content-type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['http_code' => 500, 'response_data' => ['error' => 'CURL Error: ' . $curlError]];
    }
    $decoded = json_decode($response, true);
    return ['http_code' => $httpCode, 'response_data' => $decoded ?: ['raw_response' => $response]];
}

function callShopEstabAPI($apiKey, $bearerToken, $registrationNumber, $state)
{
    $url = API_BASE_URL . SHOP_ESTAB_URL;
    $payload = json_encode(['registrationNumber' => $registrationNumber, 'state' => $state]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . trim($bearerToken),
        'apikey: ' . $apiKey,
        'content-type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['http_code' => 500, 'response_data' => ['error' => 'CURL Error: ' . $curlError]];
    }
    $decoded = json_decode($response, true);
    return ['http_code' => $httpCode, 'response_data' => $decoded ?: ['raw_response' => $response]];
}

function callEpfUanAPI($apiKey, $bearerToken, $uan)
{
    $url = API_BASE_URL . EPF_UAN_URL;
    $payload = json_encode(['uan' => $uan]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . trim($bearerToken),
        'apikey: ' . $apiKey,
        'content-type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['http_code' => 500, 'response_data' => ['error' => 'CURL Error: ' . $curlError]];
    }
    $decoded = json_decode($response, true);
    return ['http_code' => $httpCode, 'response_data' => $decoded ?: ['raw_response' => $response]];
}

function callProteanAPI($accessToken, $apiKey, $requestData)
{
    $url = API_BASE_URL . PHONE_KYC_URL;
    $jsonData = json_encode($requestData);

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
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['http_code' => 500, 'response_data' => ['error' => 'CURL Error: ' . $curlError]];
    }

    $decodedResponse = json_decode($response, true);

    return [
        'http_code' => $httpCode,
        'response_data' => $decodedResponse ?: ['raw_response' => $response]
    ];
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
                        $result .= "<div class='error'><strong>❌ " . ($decryptedError['error'] ?? 'Unknown error') . "</strong></div>";
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

        .btn-secondary {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            margin-left: 10px;
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

        .error-details {
            color: #c05621;
            background: #fffaf0;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #e53e3e;
        }

        .info {
            color: #3182ce;
            background: #ebf8ff;
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

        .key-status {
            background: #fef5e7;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .key-status .valid {
            color: #38a169;
        }

        .key-status .invalid {
            color: #e53e3e;
        }

        .key-box {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }

        .key-box pre {
            background: #1a202c;
            color: #68d391;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 11px;
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

        /* Show first panel by default before JS runs */
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

        .badge-encrypted {
            background: #ebf8ff;
            color: #2b6cb0;
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
                            <option value="silent_verification" <?php echo (($_POST['selected_api'] ?? '') === 'silent_verification' || ($_POST['selected_api'] ?? '') === '') ? 'selected' : ''; ?>>1. Mobile Silent Verification</option>
                            <option value="generate_otp" <?php echo (($_POST['selected_api'] ?? '') === 'generate_otp') ? 'selected' : ''; ?>>2. Mobile Verification with OTP - Generate OTP</option>
                            <option value="geo_fencing" <?php echo (($_POST['selected_api'] ?? '') === 'geo_fencing') ? 'selected' : ''; ?>>3. Geo Fencing</option>
                            <option value="reverse_geocode" <?php echo (($_POST['selected_api'] ?? '') === 'reverse_geocode') ? 'selected' : ''; ?>>4. Reverse Geocode</option>
                            <option value="kyc_ocr" <?php echo (($_POST['selected_api'] ?? '') === 'kyc_ocr') ? 'selected' : ''; ?>>5. KYC OCR Plus</option>
                            <option value="bank_verify" <?php echo (($_POST['selected_api'] ?? '') === 'bank_verify') ? 'selected' : ''; ?>>6.1 Dynamic Bank Account Verification</option>
                            <option value="bank_verify_amount" <?php echo (($_POST['selected_api'] ?? '') === 'bank_verify_amount') ? 'selected' : ''; ?>>6.2 Dynamic Bank Account Verification - Verify Amount</option>
                            <option value="shop_estab" <?php echo (($_POST['selected_api'] ?? '') === 'shop_estab') ? 'selected' : ''; ?>>7. Shop and Establishment</option>
                            <option value="epf_uan" <?php echo (($_POST['selected_api'] ?? '') === 'epf_uan') ? 'selected' : ''; ?>>8. EPF UAN Validation</option>
                        </select>
                    </div>

                    <!-- API 1: Silent Verification -->
                    <div class="api-fields" id="fields_silent_verification">
                        <div class="api-desc">📡 <strong>Mobile Silent Verification</strong> <span class="badge badge-encrypted">Encrypted</span><br>Calls <code>POST /api/v1/protean/phones/phone-kyc-non-consent</code> with full RSA+AES encryption.</div>
                        <div class="form-group">
                            <label>📱 Mobile Number <span style="color:#e53e3e">*</span></label>
                            <input type="text" name="mobile_number" id="sv_mobile" value="<?php echo htmlspecialchars($_POST['mobile_number'] ?? ''); ?>" placeholder="Enter 10-digit mobile number" maxlength="10">
                        </div>
                        <div class="form-group">
                            <label>📋 Additional Details</label>
                            <select name="additional_details">
                                <option value="yes" <?php echo (($_POST['additional_details'] ?? 'yes') == 'yes') ? 'selected' : ''; ?>>Yes</option>
                                <option value="no" <?php echo (($_POST['additional_details'] ?? 'yes') == 'no') ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <!-- API 2: Generate OTP -->
                    <div class="api-fields" id="fields_generate_otp">
                        <div class="api-desc">📨 <strong>Mobile Verification with OTP - Generate OTP</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/phone/generateOtp</code> — simple JSON request, no encryption.</div>
                        <div class="row">
                            <div class="form-group" style="max-width:160px">
                                <label>🌍 Country Code <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="country_code" value="<?php echo htmlspecialchars($_POST['country_code'] ?? '91'); ?>" placeholder="91" maxlength="5">
                            </div>
                            <div class="form-group">
                                <label>📱 Mobile Number <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="mobile_number" value="<?php echo htmlspecialchars($_POST['mobile_number'] ?? ''); ?>" placeholder="Enter 10-digit mobile number" maxlength="10">
                            </div>
                        </div>
                    </div>

                    <!-- API 3: Geo Fencing -->
                    <div class="api-fields" id="fields_geo_fencing">
                        <div class="api-desc">🌍 <strong>Geo Fencing</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/patrons/riskscores</code> — checks IP, country and state risk score.</div>
                        <div class="form-group">
                            <label>🎯 Task <span style="color:#e53e3e">*</span></label>
                            <input type="text" name="geo_task" value="<?php echo htmlspecialchars($_POST['geo_task'] ?? 'geoFencing'); ?>" placeholder="geoFencing">
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label>🖥️ IP Address <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="geo_ip" value="<?php echo htmlspecialchars($_POST['geo_ip'] ?? ''); ?>" placeholder="e.g. 14.xxx.xxx.xxx">
                            </div>
                            <div class="form-group" style="max-width:140px">
                                <label>🌐 Country <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="geo_country" value="<?php echo htmlspecialchars($_POST['geo_country'] ?? 'IN'); ?>" placeholder="IN" maxlength="5">
                            </div>
                            <div class="form-group" style="max-width:140px">
                                <label>📍 State <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="geo_state" value="<?php echo htmlspecialchars($_POST['geo_state'] ?? ''); ?>" placeholder="MH" maxlength="10">
                            </div>
                        </div>
                    </div>

                    <!-- API 4: Reverse Geocode -->
                    <div class="api-fields" id="fields_reverse_geocode">
                        <div class="api-desc">📍 <strong>Reverse Geocode</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/geocoding/reverse-geocode</code> — converts lat/lng coordinates to an address.</div>
                        <div class="row">
                            <div class="form-group">
                                <label>📍 Latitude <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="latitude" value="<?php echo htmlspecialchars($_POST['latitude'] ?? ''); ?>" placeholder="e.g. 21.153774">
                            </div>
                            <div class="form-group">
                                <label>📍 Longitude <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="longitude" value="<?php echo htmlspecialchars($_POST['longitude'] ?? ''); ?>" placeholder="e.g. 79.042549">
                            </div>
                        </div>
                    </div>

                    <!-- API 5: KYC OCR Plus -->
                    <div class="api-fields" id="fields_kyc_ocr">
                        <div class="api-desc">🧾 <strong>KYC OCR Plus</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/utility/single-kyc</code> — provide one or more image URLs for OCR extraction.</div>
                        <div class="form-group">
                            <label>🔗 Image URLs <span style="color:#e53e3e">*</span></label>
                            <div id="kycUrlList">
                                <?php
                                $savedUrls = $_POST['kyc_urls'] ?? ['', ''];
                                foreach ($savedUrls as $i => $u): ?>
                                    <div class="kyc-url-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">
                                        <input type="text" name="kyc_urls[]" value="<?php echo htmlspecialchars($u); ?>" placeholder="https://example.com/document<?php echo $i + 1; ?>.jpg" style="flex:1">
                                        <button type="button" onclick="removeUrlRow(this)" style="background:linear-gradient(135deg,#fc8181,#e53e3e);padding:10px 14px;font-size:13px;white-space:nowrap;">✕ Remove</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" onclick="addUrlRow()" style="background:linear-gradient(135deg,#68d391,#38a169);padding:9px 18px;font-size:13px;margin-top:4px;">+ Add URL</button>
                        </div>
                    </div>

                    <!-- API 6.1: Dynamic Bank Account Verification -->
                    <div class="api-fields" id="fields_bank_verify">
                        <div class="api-desc">🏦 <strong>Dynamic Bank Account Verification</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean-variablepennydrop/bankaccountverifications/advancedverification</code></div>
                        <div class="row">
                            <div class="form-group">
                                <label>🏦 Beneficiary Account <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="beneficiary_account" value="<?php echo htmlspecialchars($_POST['beneficiary_account'] ?? ''); ?>" placeholder="e.g. XXXXXX0000XXXX">
                            </div>
                            <div class="form-group">
                                <label>🏦 Beneficiary IFSC <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="beneficiary_ifsc" value="<?php echo htmlspecialchars($_POST['beneficiary_ifsc'] ?? ''); ?>" placeholder="e.g. SBIN0001234" maxlength="11">
                            </div>
                        </div>
                    </div>

                    <!-- API 6.2: Verify Amount -->
                    <div class="api-fields" id="fields_bank_verify_amount">
                        <div class="api-desc">💳 <strong>Dynamic Bank Account Verification - Verify Amount</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean-variablepennydrop/bankaccountverification/verifytransferadvanced</code></div>
                        <div class="row">
                            <div class="form-group" style="max-width:200px">
                                <label>💰 Amount <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="verify_amount" value="<?php echo htmlspecialchars($_POST['verify_amount'] ?? ''); ?>" placeholder="e.g. 1.05">
                            </div>
                            <div class="form-group">
                                <label>🔖 Reference ID <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="verify_referenceid" value="<?php echo htmlspecialchars($_POST['verify_referenceid'] ?? ''); ?>" placeholder="e.g. XXXXXXXXXXXXX">
                            </div>
                        </div>
                    </div>

                    <!-- API 7: Shop and Establishment -->
                    <div class="api-fields" id="fields_shop_estab">
                        <div class="api-desc">🏢 <strong>Shop and Establishment</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/shop-establishment</code> — verify shop/establishment registration details.</div>
                        <div class="row">
                            <div class="form-group">
                                <label>📋 Registration Number <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="reg_number" value="<?php echo htmlspecialchars($_POST['reg_number'] ?? ''); ?>" placeholder="e.g. 20***485">
                            </div>
                            <div class="form-group" style="max-width:220px">
                                <label>📍 State <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="se_state" value="<?php echo htmlspecialchars($_POST['se_state'] ?? ''); ?>" placeholder="e.g. Delhi">
                            </div>
                        </div>
                    </div>

                    <!-- API 8: EPF UAN Validation -->
                    <div class="api-fields" id="fields_epf_uan">
                        <div class="api-desc">💼 <strong>EPF UAN Validation</strong> <span class="badge badge-rest">REST</span><br>Calls <code>POST /api/v1/protean/fetch-employment-history</code> — validate UAN and fetch employment history.</div>
                        <div class="form-group">
                            <label>💼 UAN (Universal Account Number) <span style="color:#e53e3e">*</span></label>
                            <input type="text" name="uan" value="<?php echo htmlspecialchars($_POST['uan'] ?? ''); ?>" placeholder="e.g. XXXXXXXXXXXX" maxlength="12">
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
            // Clear previous results when switching APIs manually
            if (!isInitialLoad) {
                document.querySelectorAll('.result').forEach(el => el.style.display = 'none');
            }

            document.querySelectorAll('.api-fields').forEach(el => el.classList.remove('active'));
            const target = document.getElementById('fields_' + val);
            if (target) target.classList.add('active');
            const labels = {
                silent_verification: '🚀 Send Encrypted Request',
                generate_otp: '📨 Generate OTP',
                geo_fencing: '🌍 Check Geo Fencing',
                reverse_geocode: '📍 Reverse Geocode',
                kyc_ocr: '🧾 Run KYC OCR',
                bank_verify: '🏦 Verify Bank Account',
                bank_verify_amount: '💳 Verify Transfer Amount',
                shop_estab: '🏢 Verify Shop & Establishment',
                epf_uan: '💼 Validate EPF UAN'
            };
            document.getElementById('submitBtn').textContent = labels[val] || '🚀 Send Request';
        }

        // Show correct API fields on load
        // Script is at bottom of body so DOM is ready - call directly
        (function init() {
            const sel = document.getElementById('selectedApi');
            if (sel) switchApi(sel.value, true);
        })();

        // Fallback: also bind to DOMContentLoaded in case of async edge cases
        document.addEventListener('DOMContentLoaded', function() {
            const sel = document.getElementById('selectedApi');
            if (sel) switchApi(sel.value, true);
        });

        function addUrlRow() {
            const list = document.getElementById('kycUrlList');
            const idx = list.querySelectorAll('.kyc-url-row').length + 1;
            const div = document.createElement('div');
            div.className = 'kyc-url-row';
            div.style.cssText = 'display:flex;gap:8px;margin-bottom:8px;align-items:center;';
            div.innerHTML = `<input type="text" name="kyc_urls[]" placeholder="https://example.com/document${idx}.jpg" style="flex:1">` +
                `<button type="button" onclick="removeUrlRow(this)" style="background:linear-gradient(135deg,#fc8181,#e53e3e);padding:10px 14px;font-size:13px;white-space:nowrap;">\u2715 Remove</button>`;
            list.appendChild(div);
        }

        function removeUrlRow(btn) {
            const list = document.getElementById('kycUrlList');
            if (list.querySelectorAll('.kyc-url-row').length > 1) {
                btn.parentElement.remove();
            }
        }

        function copyToClipboard(button) {
            const pre = button.previousElementSibling;
            navigator.clipboard.writeText(pre.innerText).then(function() {
                const orig = button.innerText;
                button.innerText = '✓ Copied!';
                setTimeout(() => button.innerText = orig, 2000);
            });
        }
    </script>
</body>

</html>