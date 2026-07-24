<?php

namespace App\Services\Verification;

use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

abstract class BaseVerificationService implements VerificationServiceInterface
{
    public function process(OrderItem $OrderItem): void
    {
        try {
            Log::info("Processing service ID: {$OrderItem->id}");

            $OrderItem->processing_status = 'processing';
            $OrderItem->processing_attempts = ($OrderItem->processing_attempts ?? 0) + 1;
            $OrderItem->save();

            $this->performVerification($OrderItem);

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

    abstract protected function performVerification(OrderItem $OrderItem): void;

    protected function isTestMode(): bool
    {
        return filter_var(env('TEST_MODE', false), FILTER_VALIDATE_BOOLEAN);
    }
}
