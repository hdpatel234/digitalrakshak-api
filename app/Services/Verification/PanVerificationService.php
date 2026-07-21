<?php

namespace App\Services\Verification;

use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class PanVerificationService extends BaseVerificationService
{
    protected function performVerification(OrderItem $OrderItem): void
    {
        if ($this->isTestMode()) {
            Log::info("Simulating PAN Verification for candidate ID {$OrderItem->candidate_id}");
            usleep(100000); // 100ms
        } else {
            // Actual API implementation goes here
            Log::info("Actual PAN Verification for candidate ID {$OrderItem->candidate_id}");
        }
    }
}
