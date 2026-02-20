<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="DigitalRakshak Auth APIs",
 *     version="1.0.0",
 *     description="Authentication APIs documentation"
 * )
 *
 * @OA\Server(
 *     url="/api/v1/auth",
 *     description="Auth API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

class BaseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/health",
     *     summary="Check Auth API health status",
     *     description="Returns the health status of the Auth API",
     *     operationId="authHealthCheck",
     *     tags={"Health"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Auth API is running"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="service",
     *                     type="string",
     *                     example="auth"
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string",
     *                     example="healthy"
     *                 ),
     *                 @OA\Property(
     *                     property="timestamp",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-01-15 10:30:00"
     *                 ),
     *                 @OA\Property(
     *                     property="environment",
     *                     type="string",
     *                     example="local"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function healthCheck(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Auth API is running',
            'data' => [
                'service' => 'auth',
                'status' => 'healthy',
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'environment' => app()->environment()
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/methods",
     *     summary="Get available authentication methods",
     *     description="Returns a list of available authentication methods",
     *     operationId="getAuthMethods",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="methods",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="type",
     *                             type="string",
     *                             example="email"
     *                         ),
     *                         @OA\Property(
     *                             property="enabled",
     *                             type="boolean",
     *                             example=true
     *                         ),
     *                         @OA\Property(
     *                             property="description",
     *                             type="string",
     *                             example="Login with email and password"
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="features",
     *                     type="object",
     *                     @OA\Property(
     *                         property="registration",
     *                         type="boolean",
     *                         example=true
     *                     ),
     *                     @OA\Property(
     *                         property="password_reset",
     *                         type="boolean",
     *                         example=true
     *                     ),
     *                     @OA\Property(
     *                         property="email_verification",
     *                         type="boolean",
     *                         example=true
     *                     ),
     *                     @OA\Property(
     *                         property="social_login",
     *                         type="boolean",
     *                         example=false
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getAuthMethods(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'methods' => [
                    [
                        'type' => 'email',
                        'enabled' => true,
                        'description' => 'Login with email and password'
                    ],
                    [
                        'type' => 'token',
                        'enabled' => true,
                        'description' => 'Login with API token'
                    ]
                ],
                'features' => [
                    'registration' => true,
                    'password_reset' => true,
                    'email_verification' => true,
                    'social_login' => false
                ]
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/config",
     *     summary="Get authentication configuration",
     *     description="Returns the current authentication configuration",
     *     operationId="getAuthConfig",
     *     tags={"Configuration"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="token_ttl",
     *                     type="integer",
     *                     description="Token time to live in minutes",
     *                     example=60
     *                 ),
     *                 @OA\Property(
     *                     property="refresh_ttl",
     *                     type="integer",
     *                     description="Refresh token time to live in minutes",
     *                     example=20160
     *                 ),
     *                 @OA\Property(
     *                     property="max_attempts",
     *                     type="integer",
     *                     description="Maximum login attempts",
     *                     example=5
     *                 ),
     *                 @OA\Property(
     *                     property="lockout_duration",
     *                     type="integer",
     *                     description="Lockout duration in minutes",
     *                     example=15
     *                 ),
     *                 @OA\Property(
     *                     property="password_policy",
     *                     type="object",
     *                     @OA\Property(
     *                         property="min_length",
     *                         type="integer",
     *                         example=8
     *                     ),
     *                     @OA\Property(
     *                         property="requires_uppercase",
     *                         type="boolean",
     *                         example=true
     *                     ),
     *                     @OA\Property(
     *                         property="requires_numbers",
     *                         type="boolean",
     *                         example=true
     *                     ),
     *                     @OA\Property(
     *                         property="requires_symbols",
     *                         type="boolean",
     *                         example=false
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getAuthConfig(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'token_ttl' => config('jwt.ttl', 60),
                'refresh_ttl' => config('jwt.refresh_ttl', 20160),
                'max_attempts' => 5,
                'lockout_duration' => 15,
                'password_policy' => [
                    'min_length' => 8,
                    'requires_uppercase' => true,
                    'requires_numbers' => true,
                    'requires_symbols' => false
                ]
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/status",
     *     summary="Get authentication status",
     *     description="Returns the current authentication system status",
     *     operationId="getAuthStatus",
     *     tags={"Status"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="is_active",
     *                     type="boolean",
     *                     example=true
     *                 ),
     *                 @OA\Property(
     *                     property="mode",
     *                     type="string",
     *                     enum={"production", "maintenance", "setup"},
     *                     example="production"
     *                 ),
     *                 @OA\Property(
     *                     property="version",
     *                     type="string",
     *                     example="1.0.0"
     *                 ),
     *                 @OA\Property(
     *                     property="last_checked",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-01-15T10:30:00Z"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getStatus(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'is_active' => true,
                'mode' => 'production',
                'version' => '1.0.0',
                'last_checked' => now()->toIso8601String()
            ]
        ]);
    }
}