<?php

namespace App\Services\Verification;

use App\Models\CandidateService;
use Illuminate\Support\Facades\Log;

class PhysicalAddressVerificationService extends BaseVerificationService
{
    protected function performVerification(CandidateService $candidateService): void
    {
        if ($this->isTestMode()) {
            Log::info("Simulating Physical Address Verification for candidate ID {$candidateService->candidate_id}");
            usleep(100000); // 100ms
        } else {
            // Actual API implementation goes here
            Log::info("Actual Physical Address Verification for candidate ID {$candidateService->candidate_id}");
        }
    }
}
