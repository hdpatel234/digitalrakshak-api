<?php

namespace App\Services\Verification;

use App\Models\OrderItem;

interface VerificationServiceInterface
{
    /**
     * Process the candidate service.
     *
     * @param OrderItem $OrderItem
     * @return void
     */
    public function process(OrderItem $OrderItem): void;
}
