<?php

namespace App\Http\Controllers\Api\Client;

use App\Services\ProteanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProteanController extends BaseController
{
    protected ProteanService $proteanService;

    public function __construct(ProteanService $proteanService)
    {
        $this->proteanService = $proteanService;
    }

    /**
     * Helper to return standard JSON error or success responses.
     */
    protected function handleApiResponse(array $result): JsonResponse
    {
        if (isset($result['success']) && !$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'API execution failed.',
                'details' => $result['data'] ?? ($result['raw'] ?? null)
            ], $result['status_code'] ?? 400);
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'] ?? null
        ]);
    }

    /**
     * 1. Mobile Silent Verification
     */
    public function silentVerify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string|size:10',
            'additional_details' => 'nullable|string|in:yes,no',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->proteanService->silentVerification(
            $request->input('mobile_number'),
            $request->input('additional_details', 'yes')
        );

        return $this->handleApiResponse($result);
    }

    /**
     * 2. Generate OTP
     */
    public function generateOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string|size:10',
            'country_code' => 'nullable|string|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->proteanService->generateOtp(
            $request->input('mobile_number'),
            $request->input('country_code', '91')
        );

        return $this->handleApiResponse($result);
    }

    /**
     * 3. Geo Fencing
     */
    public function geoFencing(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ip' => 'required|string|ip',
            'state' => 'required|string|max:10',
            'country' => 'nullable|string|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->proteanService->geoFencing(
            $request->input('ip'),
            $request->input('state'),
            $request->input('country', 'IN')
        );

        return $this->handleApiResponse($result);
    }

    /**
     * 4. Reverse Geocode
     */
    public function reverseGeocode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->proteanService->reverseGeocode(
            $request->input('latitude'),
            $request->input('longitude')
        );

        return $this->handleApiResponse($result);
    }

    /**
     * 5. KYC OCR Plus
     */
    public function kycOcr(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image_urls' => 'required|array|min:1',
            'image_urls.*' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->proteanService->kycOcr(
            $request->input('image_urls')
        );

        return $this->handleApiResponse($result);
    }

    /**
     * 6.1 Dynamic Bank Account Verification
     */
    public function bankVerify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'beneficiary_account' => 'required|string',
            'beneficiary_ifsc' => 'required|string|size:11',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->proteanService->bankVerify(
            $request->input('beneficiary_account'),
            $request->input('beneficiary_ifsc')
        );

        return $this->handleApiResponse($result);
    }

    /**
     * 6.2 Dynamic Bank Account Verification - Verify Amount
     */
    public function bankVerifyAmount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'reference_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->proteanService->bankVerifyAmount(
            (float) $request->input('amount'),
            $request->input('reference_id')
        );

        return $this->handleApiResponse($result);
    }

    /**
     * 7. Shop and Establishment
     */
    public function shopEstablishment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'registration_number' => 'required|string',
            'state' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->proteanService->shopEstablishment(
            $request->input('registration_number'),
            $request->input('state')
        );

        return $this->handleApiResponse($result);
    }

    /**
     * 8. EPF UAN Validation
     */
    public function epfUan(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'uan' => 'required|string|size:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->proteanService->epfUanValidation(
            $request->input('uan')
        );

        return $this->handleApiResponse($result);
    }
}
