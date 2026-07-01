<?php

namespace App\Services\Verification;

use App\Models\CandidateService;
use Illuminate\Support\Facades\Log;

class EmploymentVerificationService extends BaseVerificationService
{
    protected function performVerification(CandidateService $candidateService): void
    {
        if ($this->isTestMode()) {
            Log::info("Simulating Employment Verification for candidate ID {$candidateService->candidate_id}");
            // Sleep slightly to simulate processing
            usleep(100000); // 100ms
        } else {
            // Actual API implementation goes here
            Log::info("Actual Employment Verification for candidate ID {$candidateService->candidate_id}");
        }
    }
}
