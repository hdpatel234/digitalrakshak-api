<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Api\Auth\LoginRequest;
use App\Services\Api\Auth\LoginService;
use App\Traits\ApiResponse;

class LoginController extends BaseController
{
    use ApiResponse;

    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="User login",
     *     tags={"Login"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@digitalrakshak.com"),
     *             @OA\Property(property="password", type="string", format="password", example="User@123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600),                         
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="first_name", type="string", example="John"),
     *                     @OA\Property(property="last_name", type="string", example="Doe"),
     *                     @OA\Property(property="email", type="string", example="user@digitalrakshak.com"),
     *                     @OA\Property(property="last_login_at", type="string", example="0000-00-00 00:00:00"),
     *                     @OA\Property(property="last_login_ip", type="string", example="127.0.0.1"),
     *                     @OA\Property(property="last_login_browser", type="string", example="Chrome"),
     *                     @OA\Property(property="last_login_device", type="string", example="Windows 10"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="is_admin", type="boolean", example=false),
     *                     @OA\Property(property="created_at", type="string", example="0000-00-00 00:00:00"),  
     *                 ),
     *                 @OA\Property(property="timestamp", type="string", example="0000-00-00 00:00:00"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials"),
     *             @OA\Property(property="data", type="object", example={}),
     *             @OA\Property(property="timestamp", type="string", example="0000-00-00 00:00:00"),          
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *         @OA\Property(property="success", type="boolean", example=false),
     *         @OA\Property(property="message", type="string", example="Validation error"),
     *         @OA\Property(
     *             property="errors",
     *             type="object",
     *             @OA\Property(
     *                 property="email",
     *                 type="array",
     *                 @OA\Items(type="string", example="The email field is required.")
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="array",
     *                 @OA\Items(type="string", example="The password field is required.")
     *             )
     *         ),
     *         @OA\Property(property="timestamp", type="string", example="0000-00-00 00:00:00")
     *     )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $result = $this->loginService->login($request->email, $request->password);

        if ($result['status'] == false) {
            return $this->error($result['message']);
        }

        return $this->success($result['message'], $result['data']);
    }
}
