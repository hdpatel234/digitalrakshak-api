<?php

namespace App\Services\Verification;

use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class EducationVerificationService extends BaseVerificationService
{
    protected function performVerification(OrderItem $OrderItem): void
    {
        if ($this->isTestMode()) {
            Log::info("Simulating Education Verification for candidate ID {$OrderItem->candidate_id}");
            usleep(100000); // 100ms
        } else {
            // Actual API implementation goes here
            Log::info("Actual Education Verification for candidate ID {$OrderItem->candidate_id}");
        }
    }
}
