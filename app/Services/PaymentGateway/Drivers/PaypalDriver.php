<?php

namespace App\Services\PaymentGateway\Drivers;

use InvalidArgumentException;

class PaypalDriver extends AbstractPaymentGatewayDriver
{
    public function initiatePayment(array $payload): array
    {
        throw new InvalidArgumentException('PayPal payment driver is not implemented yet.');
    }
}
