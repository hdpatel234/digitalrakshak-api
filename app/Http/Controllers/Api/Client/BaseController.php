<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="DigitalRakshak Client APIs",
 *     version="1.0.0",
 *     description="Client APIs documentation"
 * )
 *
 * @OA\Server(
 *     url="L5_SWAGGER_CONST_CLIENT_BASE_URL",
 *     description="Client API Server"
 * )
 *
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
     *     summary="Check Client API health status",
     *     description="Returns the health status of the Client API",
     *     operationId="clientHealthCheck",
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
     *                 example="Client API is running"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="service",
     *                     type="string",
     *                     example="client"
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
     *                 ),
     *                 @OA\Property(
     *                     property="version",
     *                     type="string",
     *                     example="1.0.0"
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
            'message' => 'Client API is running',
            'data' => [
                'service' => 'client',
                'status' => 'healthy',
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'environment' => app()->environment(),
                'version' => '1.0.0'
            ]
        ]);
    }
}