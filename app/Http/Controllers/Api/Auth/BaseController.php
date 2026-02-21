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
 *     url=L5_SWAGGER_CONST_AUTH_BASE_URL,
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
    //
}
