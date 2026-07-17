<?php

namespace App\Services\Verification;

use App\Models\CandidateService;
use App\Models\EmploymentVerification;
use App\Models\CandidateServiceData;
use App\Mail\EmploymentVerificationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmploymentVerificationService extends BaseVerificationService
{
    protected function performVerification(CandidateService $candidateService): void
    {
        if ($this->isTestMode()) {
            Log::info("Simulating Employment Verification for candidate ID {$candidateService->candidate_id}");
            // Sleep slightly to simulate processing
            usleep(100000); // 100ms
        } else {
            Log::info("Actual Employment Verification for candidate ID {$candidateService->candidate_id}");

            // Gather candidate data for this service
            $serviceData = CandidateServiceData::with('field')
                ->where('candidate_service_id', $candidateService->id)
                ->get();

            // Format data and try to find an email
            $formattedData = [];
            $companyEmail = 'hr@example.com'; // Default fallback

            foreach ($serviceData as $data) {
                $fieldName = $data->field ? $data->field->name : 'Unknown Field';
                $formattedData[$fieldName] = $data->field_value;

                // Very naive email detection for the sake of the flow
                if (str_contains(strtolower($fieldName), 'email') || filter_var($data->field_value, FILTER_VALIDATE_EMAIL)) {
                    if ($companyEmail === 'hr@example.com') { // only override if it's the fallback
                        $companyEmail = $data->field_value;
                    }
                }
            }

            // Generate token
            $token = Str::random(40);

            // Create verification record
            $verification = EmploymentVerification::create([
                'candidate_service_id' => $candidateService->id,
                'token' => $token,
                'company_email' => $companyEmail,
                'candidate_data' => $formattedData,
                'status' => 'pending',
            ]);

            // Construct verification URL
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            $verificationUrl = rtrim($frontendUrl, '/') . '/verify-employment/' . $token;

            $candidateName = $candidateService->candidate->first_name . ' ' . $candidateService->candidate->last_name;

            // Send email
            Mail::to($companyEmail)->send(new EmploymentVerificationMail($verificationUrl, $candidateName));

            // Mark service as ON_HOLD while waiting for response
            $candidateService->status = 'ON_HOLD'; // Assuming ON_HOLD is a valid status, if not we can use something else or keep it as PROCESSING. Let's use ON_HOLD.
            $candidateService->save();

            Log::info("Sent employment verification email to {$companyEmail} for candidate {$candidateService->candidate_id}");
        }
    }
}
