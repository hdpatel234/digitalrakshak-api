<?php

namespace App\Services\Email;

use App\Models\EmailServer;
use App\Services\Email\Drivers\SendGridDriver;
use App\Services\Email\Drivers\SmtpDriver;

class EmailDriverFactory
{
    public function driver(EmailServer $server)
    {
        switch ($server->serverType->type_code) {
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
                throw new \InvalidArgumentException("Unsupported email server type: {$server->serverType->type_code}");
        }
    }
}
