<?php

use App\Services\PaymentGateway\Drivers\PaypalDriver;
use App\Services\PaymentGateway\Drivers\RazorpayDriver;
use App\Services\PaymentGateway\Drivers\StripeDriver;

return [
    'drivers' => [
        'razorpay' => RazorpayDriver::class,
        'stripe' => StripeDriver::class,
        'paypal' => PaypalDriver::class,
    ],
];
