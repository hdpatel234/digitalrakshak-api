<?php

namespace App\Services\ApiService\Client;

use App\Models\EmploymentVerification;
use Illuminate\Support\Carbon;

class EmploymentVerificationService
{
    public function getVerificationDetails(string $token)
    {
        $verification = EmploymentVerification::with('candidateService.candidate')
            ->where('token', $token)
            ->first();

        if (!$verification) {
            throw new \Exception('Invalid or expired verification link.', 404);
        }

        return [
            'candidate_name' => $verification->candidateService->candidate->first_name . ' ' . $verification->candidateService->candidate->last_name,
            'company_email' => $verification->company_email,
            'candidate_data' => $verification->candidate_data,
            'verification_status' => $verification->status,
            'remarks' => $verification->remarks,
        ];
    }

    public function processVerification(string $token, array $data)
    {
        $verification = EmploymentVerification::where('token', $token)->first();

        if (!$verification) {
            throw new \Exception('Invalid or expired verification link.', 404);
        }

        if ($verification->status !== 'pending') {
            throw new \Exception('This verification has already been processed.', 400);
        }

        $verification->update([
            'status' => $data['status'],
            'remarks' => $data['remarks'] ?? null,
            'verified_at' => Carbon::now(),
        ]);

        // Update the related candidate service
        $candidateService = $verification->candidateService;
        if ($candidateService) {
            // Update service status depending on verification response
            if ($data['status'] === 'verified') {
                $candidateService->status = 'COMPLETED'; // Assuming COMPLETED is the end state
            } else {
                $candidateService->status = 'ERROR'; // or 'NEEDS_ATTENTION' depending on your constants
                $candidateService->error_message = 'Verification ' . $data['status'] . '. Remarks: ' . ($data['remarks'] ?? 'None');
            }
            $candidateService->save();
        }

        return true;
    }
}
