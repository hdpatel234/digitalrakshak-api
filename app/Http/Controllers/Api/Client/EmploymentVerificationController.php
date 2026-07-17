<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\EmploymentVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class EmploymentVerificationController extends Controller
{
    /**
     * Display the employment verification details for the given token.
     */
    public function show(string $token): JsonResponse
    {
        $verification = EmploymentVerification::with('candidateService.candidate')
            ->where('token', $token)
            ->first();

        if (!$verification) {
            return response()->json(['message' => 'Invalid or expired verification link.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'candidate_name' => $verification->candidateService->candidate->first_name . ' ' . $verification->candidateService->candidate->last_name,
                'company_email' => $verification->company_email,
                'candidate_data' => $verification->candidate_data,
                'verification_status' => $verification->status,
                'remarks' => $verification->remarks,
            ]
        ]);
    }

    /**
     * Submit the verification response.
     */
    public function verify(Request $request, string $token): JsonResponse
    {
        $verification = EmploymentVerification::where('token', $token)->first();

        if (!$verification) {
            return response()->json(['message' => 'Invalid or expired verification link.'], 404);
        }

        if ($verification->status !== 'pending') {
            return response()->json(['message' => 'This verification has already been processed.'], 400);
        }

        $request->validate([
            'status' => 'required|in:verified,rejected,needs_changes',
            'remarks' => 'nullable|string'
        ]);

        $verification->update([
            'status' => $request->input('status'),
            'remarks' => $request->input('remarks'),
            'verified_at' => Carbon::now(),
        ]);

        // Update the related candidate service
        $candidateService = $verification->candidateService;
        if ($candidateService) {
            // Update service status depending on verification response
            if ($request->input('status') === 'verified') {
                $candidateService->status = 'COMPLETED'; // Assuming COMPLETED is the end state
            } else {
                $candidateService->status = 'ERROR'; // or 'NEEDS_ATTENTION' depending on your constants
                $candidateService->error_message = 'Verification ' . $request->input('status') . '. Remarks: ' . $request->input('remarks');
            }
            $candidateService->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Verification response submitted successfully.',
        ]);
    }
}
