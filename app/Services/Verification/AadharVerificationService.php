<?php

namespace App\Services\Verification;

use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class AadharVerificationService extends BaseVerificationService
{
    protected function performVerification(OrderItem $OrderItem): void
    {
        if ($this->isTestMode()) {
            Log::info("Simulating Aadhar Verification for candidate ID {$OrderItem->candidate_id}");
            usleep(100000); // 100ms
        } else {
            // Actual API implementation goes here
            Log::info("Actual Aadhar Verification for candidate ID {$OrderItem->candidate_id}");
        }
    }
}
