<?php

namespace App\Services\PaymentGateway;

use App\Models\PaymentGatewayConfig;
use App\Services\PaymentGateway\Drivers\AbstractPaymentGatewayDriver;
use App\Services\PaymentGateway\Drivers\PaypalDriver;
use App\Services\PaymentGateway\Drivers\RazorpayDriver;
use App\Services\PaymentGateway\Drivers\StripeDriver;
use InvalidArgumentException;
use RuntimeException;

class PaymentGatewayDriverFactory
{
    public function driver(PaymentGatewayConfig $gatewayConfig): AbstractPaymentGatewayDriver
    {
        $gatewayConfig->loadMissing('gateway');

        $gateway = $gatewayConfig->gateway;
        if (!$gateway) {
            throw new RuntimeException('Payment gateway is not configured.');
        }

        $gatewayKey = $this->normalizeKey((string) ($gateway->gateway_code ?? $gateway->gateway_name ?? ''));
        if ($gatewayKey === '') {
            throw new InvalidArgumentException('Payment gateway code is not configured.');
        }

        $drivers = config('payment_gateways.drivers', [
            'razorpay' => RazorpayDriver::class,
            'stripe' => StripeDriver::class,
            'paypal' => PaypalDriver::class,
        ]);

        $driverClass = $drivers[$gatewayKey] ?? null;

        if (!is_string($driverClass) || !class_exists($driverClass)) {
            throw new InvalidArgumentException("Unsupported payment gateway [$gatewayKey].");
        }

        if (!is_subclass_of($driverClass, AbstractPaymentGatewayDriver::class)) {
            throw new InvalidArgumentException("Payment gateway driver [$driverClass] must extend " . AbstractPaymentGatewayDriver::class . '.');
        }

        return new $driverClass($gatewayConfig);
    }

    protected function normalizeKey(string $value): string
    {
        $value = strtolower(trim($value));
        if ($value === '') {
            return '';
        }

        $value = preg_replace('/[^a-z0-9]+/', '_', $value);
        return trim((string) $value, '_');
    }
}
