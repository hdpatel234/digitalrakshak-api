<?php

namespace App\Services\Verification;

use App\Models\OrderItem;
use App\Models\CandidateServiceData;
use Illuminate\Support\Facades\Log;

abstract class BaseVerificationService implements VerificationServiceInterface
{
    /**
     * Process the candidate service.
     *
     * @param OrderItem $OrderItem
     * @return void
     */
    public function process(OrderItem $OrderItem): void
    {
        try {
            // Log that processing has started
            Log::info("Processing service ID: {$OrderItem->id}");
            
            // Mark as processing
            $OrderItem->processing_status = 'processing';
            $OrderItem->processing_attempts = ($OrderItem->processing_attempts ?? 0) + 1;
            $OrderItem->save();

            // Actual specific logic
            $this->performVerification($OrderItem);

            // Mark as completed
            $OrderItem->processing_status = 'completed';
            $OrderItem->status = 'completed';
            $OrderItem->processed_at = now();
            $OrderItem->completed_at = now();
            $OrderItem->save();

            Log::info("Successfully processed service ID: {$OrderItem->id}");

        } catch (\Exception $e) {
            Log::error("Error processing service ID {$OrderItem->id}: " . $e->getMessage());
            
            $OrderItem->processing_status = 'failed';
            $OrderItem->error_message = $e->getMessage();
            $OrderItem->save();
        }
    }

    /**
     * Perform the actual verification for the service.
     * To be implemented by child classes.
     *
     * @param OrderItem $OrderItem
     * @return void
     */
    abstract protected function performVerification(OrderItem $OrderItem): void;

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
