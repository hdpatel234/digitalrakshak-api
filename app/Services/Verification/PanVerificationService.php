<?php

namespace App\Services\Verification;

use App\Models\CandidateService;
use Illuminate\Support\Facades\Log;

class PanVerificationService extends BaseVerificationService
{
    protected function performVerification(CandidateService $candidateService): void
    {
        if ($this->isTestMode()) {
            Log::info("Simulating PAN Verification for candidate ID {$candidateService->candidate_id}");
            usleep(100000); // 100ms
        } else {
            // Actual API implementation goes here
            Log::info("Actual PAN Verification for candidate ID {$candidateService->candidate_id}");
        }
    }
}
