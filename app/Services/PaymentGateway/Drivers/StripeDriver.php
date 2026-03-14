<?php

namespace App\Services\PaymentGateway\Drivers;

use InvalidArgumentException;

class StripeDriver extends AbstractPaymentGatewayDriver
{
    public function initiatePayment(array $payload): array
    {
        throw new InvalidArgumentException('Stripe payment driver is not implemented yet.');
    }
}
