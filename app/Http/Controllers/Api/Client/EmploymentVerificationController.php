<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Requests\Api\Client\VerifyEmploymentRequest;
use App\Services\ApiService\Client\EmploymentVerificationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class EmploymentVerificationController extends BaseController
{
    use ApiResponse;

    public function __construct(protected EmploymentVerificationService $employmentVerificationService) {}

    /**
     * Display the employment verification details for the given token.
     */
    public function show(string $token): JsonResponse
    {
        addInfoLog("Client employment verification show request, Token: {$token}");

        try {
            $data = $this->employmentVerificationService->getVerificationDetails($token);
            return $this->success('Employment verification details fetched successfully.', $data);
        } catch (\Exception $e) {
            $code = $e->getCode() === 404 ? 404 : 500;
            return $this->error($e->getMessage(), $code);
        }
    }

    /**
     * Submit the verification response.
     */
    public function verify(VerifyEmploymentRequest $request, string $token): JsonResponse
    {
        addInfoLog("Client employment verification submit request, Token: {$token}");

        try {
            $this->employmentVerificationService->processVerification($token, $request->validated());
            return $this->success('Verification response submitted successfully.');
        } catch (\Exception $e) {
            $code = in_array($e->getCode(), [400, 404]) ? $e->getCode() : 500;
            return $this->error($e->getMessage(), $code);
        }
    }
}
