<?php

namespace App\Services\Verification;

use App\Models\CandidateService;

interface VerificationServiceInterface
{
    /**
     * Process the candidate service.
     *
     * @param CandidateService $candidateService
     * @return void
     */
    public function process(CandidateService $candidateService): void;
}
