<?php

namespace App\Services\EmailServerTesting;

use App\Services\EmailServerTesting\Testers\SmtpTester;
use App\Services\EmailServerTesting\Testers\DummyTester;

class EmailServerTesterFactory
{
    /**
     * Make a tester instance based on the server type code.
     *
     * @param string|null $typeCode
     * @return EmailServerTesterInterface
     */
    public static function make(?string $typeCode): EmailServerTesterInterface
    {
        $code = strtolower(trim((string)$typeCode));
        
        switch ($code) {
            case 'smtp':
                return new SmtpTester();
            // Add more cases here for Mailgun, Sendgrid, etc.
            default:
                return new DummyTester();
        }
    }
}
