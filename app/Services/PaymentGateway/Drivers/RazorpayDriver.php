<?php

namespace App\Services\PaymentGateway\Drivers;

use Razorpay\Api\Api;

class RazorpayDriver extends AbstractPaymentGatewayDriver
{
    public function initiatePayment(array $payload): array
    {
        $amount = (float) ($payload['amount'] ?? 0);
        $amountInPaise = (int) ($payload['amount_in_paise'] ?? 0);
        $currency = (string) ($payload['currency'] ?? 'INR');

        if ($amountInPaise <= 0 && $amount > 0) {
            $amountInPaise = (int) round($amount * 100);
        }

        if ($amountInPaise <= 0) {
            throw new \InvalidArgumentException('Amount in paise must be greater than zero.');
        }

        $apiKey = (string) $this->requireConfig('api_key');
        $apiSecret = (string) $this->requireConfig('api_secret');
        $receipt = (string) ($payload['receipt'] ?? ('receipt_' . now()->format('YmdHis')));

        $api = new Api($apiKey, $apiSecret);
        $order = $api->order->create([
            'amount' => $amountInPaise,
            'currency' => strtoupper($currency),
            'receipt' => $receipt,
        ]);

        return [
            'provider' => 'razorpay',
            'key' => $apiKey,
            'gateway_order_id' => $order->id ?? null,
            'amount' => $amount,
            'amount_in_paise' => $amountInPaise,
            'currency' => $order->currency ?? strtoupper($currency),
            'order_number' => (string) ($payload['order_number'] ?? ''),
            'notes' => [
                'order_id' => (int) ($payload['order_id'] ?? 0),
            ],
            'razorpay_order' => $order->toArray(),
        ];
    }
}
