<?php

namespace App\Services\Verification;

use App\Models\CandidateService;
use App\Models\CandidateServiceData;
use Illuminate\Support\Facades\Log;

abstract class BaseVerificationService implements VerificationServiceInterface
{
    /**
     * Process the candidate service.
     *
     * @param CandidateService $candidateService
     * @return void
     */
    public function process(CandidateService $candidateService): void
    {
        try {
            // Log that processing has started
            Log::info("Processing service ID: {$candidateService->id}");
            
            // Mark as processing
            $candidateService->processing_status = 'processing';
            $candidateService->processing_attempts = ($candidateService->processing_attempts ?? 0) + 1;
            $candidateService->save();

            // Actual specific logic
            $this->performVerification($candidateService);

            // Mark as completed
            $candidateService->processing_status = 'completed';
            $candidateService->status = 'completed';
            $candidateService->processed_at = now();
            $candidateService->completed_at = now();
            $candidateService->save();

            Log::info("Successfully processed service ID: {$candidateService->id}");

        } catch (\Exception $e) {
            Log::error("Error processing service ID {$candidateService->id}: " . $e->getMessage());
            
            $candidateService->processing_status = 'failed';
            $candidateService->error_message = $e->getMessage();
            $candidateService->save();
        }
    }

    /**
     * Perform the actual verification for the service.
     * To be implemented by child classes.
     *
     * @param CandidateService $candidateService
     * @return void
     */
    abstract protected function performVerification(CandidateService $candidateService): void;

    /**
     * Check if we are running in TEST_MODE.
     *
     * @return bool
     */
    protected function isTestMode(): bool
    {
        return filter_var(env('TEST_MODE', false), FILTER_VALIDATE_BOOLEAN);
    }
}
