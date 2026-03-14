<?php

namespace App\Services\PaymentGateway\Drivers;

use App\Models\PaymentGatewayConfig;
use InvalidArgumentException;

abstract class AbstractPaymentGatewayDriver
{
    public function __construct(protected PaymentGatewayConfig $gatewayConfig)
    {
    }

    abstract public function initiatePayment(array $payload): array;

    protected function requireConfig(string $key): mixed
    {
        $value = $this->gatewayConfig->{$key} ?? null;

        if (blank($value)) {
            throw new InvalidArgumentException("Payment gateway configuration value [$key] is required.");
        }

        return $value;
    }

    protected function configValue(string $key, mixed $default = null): mixed
    {
        $value = $this->gatewayConfig->{$key} ?? null;

        return blank($value) ? $default : $value;
    }
}
