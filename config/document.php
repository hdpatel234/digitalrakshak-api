<?php

use App\Services\Document\Drivers\NextcloudDriver;

return [
    'drivers' => [
        'nextcloud' => NextcloudDriver::class,
    ],
];
