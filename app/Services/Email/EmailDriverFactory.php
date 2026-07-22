<?php

namespace App\Services\Email;

use App\Models\EmailServer;
use App\Models\EmailServerConfigurationField;
use App\Models\EmailServerConfigurationValue;
use App\Services\Email\Drivers\SendGridDriver;
use App\Services\Email\Drivers\SmtpDriver;
use Exception;

use App\Services\Email\Contracts\EmailDriverInterface;

class EmailDriverFactory
{
    public function make(EmailServer $server): EmailDriverInterface
    {
        switch (strtolower(trim((string)$server->serverType?->type_code))) {
            case 'smtp':
                return new SmtpDriver($server);
            case 'sendgrid':
                return new SendGridDriver($server);
            // case 'mailgun':
            //     return new MailgunDriver($server);
            // case 'ses':
            //     return new SesDriver($server);
            // case 'postmark':
            //     return new PostmarkDriver($server);
            default:
                throw new \InvalidArgumentException("Unsupported email server type: {$server->serverType?->type_code}");
        }
    }

    public function testConnection(EmailServer $server): array
    {
        return $this->make($server)->testConnection();
    }
}

