<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="DigitalRakshak Admin APIs",
 *     version="1.0.0",
 *     description="Admin APIs documentation"
 * )
 *
 * @OA\Server(
 *     url="L5_SWAGGER_CONST_ADMIN_BASE_URL",
 *     description="Admin API Server"
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
     *     summary="Check API health status",
     *     description="Returns the health status of the API",
     *     operationId="healthCheck",
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
     *                 example="API is running"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
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
            'message' => 'API is running',
            'data' => [
                'status' => 'healthy',
                'timestamp' => now()->format((string) config('app.user_datetime_format', 'Y-m-d H:i:s')),
                'environment' => app()->environment()
            ]
        ]);
    }
}
