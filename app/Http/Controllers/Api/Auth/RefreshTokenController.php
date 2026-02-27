<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Api\Auth\RefreshTokenRequest;
use App\Services\RefreshTokenService;
use App\Traits\ApiResponse;

class RefreshTokenController extends BaseController
{
    use ApiResponse;
    protected $service;

    public function __construct(RefreshTokenService $service)
    {
        $this->service = $service;
    }
    /**
     * @OA\Post(
     *     path="/refresh-token",
     *     summary="Refresh access token",
     *     description="Generate a new access token using a valid refresh token",
     *     operationId="refreshToken",
     *     security={{"bearerAuth":{}}},
     *     tags={"Refresh Token"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"refresh_token"},
     *             @OA\Property(
     *                 property="refresh_token",
     *                 type="string",
     *                 example="def50200c75113d2dcae2203b8cd31ceaab6b4e0d7c8430c45f60e2f4d4765....",
     *                 description="Refresh token ID returned during login"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token refreshed successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600),
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="refresh_token", type="string", example="def50200c75113d2dcae2203b8cd31ceaab6b4e0d7c8430c45f60e2f4d4765...."),
     *             ),
     *             @OA\Property(property="timestamp", type="string", example="0000-00-00 00:00:00")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid refresh token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid refresh token"),
     *             @OA\Property(property="data", type="object", example={}),
     *             @OA\Property(property="timestamp", type="string", example="0000-00-00 00:00:00")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"refresh_token": {"The refresh token field is required."}}
     *             ),
     *             @OA\Property(property="timestamp", type="string", example="0000-00-00 00:00:00")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to refresh token"),
     *             @OA\Property(property="timestamp", type="string", example="0000-00-00 00:00:00")
     *         )
     *     )
     * )
     */
    public function refreshToken(RefreshTokenRequest $request)
    {
        $result = $this->service->refreshToken($request->refresh_token);

        if ($result['status'] == false) {
            return $this->error($result['message']);
        }

        return $this->success($result['message'], $result['data']);
    }
}