<?php

use App\Services\Billing\Drivers\InvoiceNinjaDriver;

return [
    'drivers' => [
        'invoice_ninja' => InvoiceNinjaDriver::class,
    ],
];

