<?php

namespace App\Http\Controllers\Api\Auth;

use App\Services\Api\Auth\LogoutService;
use App\Traits\ApiResponse;


class LogoutController extends BaseController
{
    use ApiResponse;
    protected $logoutService;

    public function __construct(LogoutService $logoutService)
    {
        $this->logoutService = $logoutService;
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Logout current user",
     *     description="Logs out the authenticated user by invalidating the current session/token.",
     *     tags={"Logout"},
     *     security={{"bearerAuth":{}}},
     * 
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully logged out"),
     *             @OA\Property(property="data", type="object", example={}),
     *             @OA\Property(property="timestamp", type="string", example="0000-00-00 00:00:00")
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        $result = $this->logoutService->logout(auth()->user());

        if ($result['status'] == false) {
            return $this->error($result['message']);
        }

        return $this->success($result['message']);
    }

    /**
     * @OA\Post(
     *     path="/logout-all",
     *     summary="Logout from all devices",
     *     description="Revokes all access tokens of the authenticated user and logs out from all devices.",
     *     tags={"Logout"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out from all devices",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully logged out from all devices"),
     *             @OA\Property(property="data", type="object", example={}),
     *             @OA\Property(property="timestamp", type="string", example="0000-00-00 00:00:00")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function logoutAll()
    {
        $result = $this->logoutService->logoutAll(auth()->user());

        if ($result['status'] == false) {
            return $this->error($result['message']);
        }

        return $this->success($result['message']);
    }
}