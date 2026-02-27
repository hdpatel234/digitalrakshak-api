<?php

namespace App\Services\Email\Drivers;

use App\Models\EmailServer;
use Illuminate\Support\Facades\Mail;
use App\Mail\DynamicEmail;

class SmtpDriver
{
    protected $server;

    public function __construct(EmailServer $server)
    {
        $this->server = $server;
        $this->configureSmtp();
    }

    protected function configureSmtp()
    {
        config([
            'mail.mailers.smtp' => [
                'transport' => 'smtp',
                'host' => $this->server->host,
                'port' => $this->server->port,
                'encryption' => $this->server->encryption,
                'username' => $this->server->username,
                'password' => decrypt($this->server->password),
                'timeout' => $this->server->timeout,
                'verify_ssl' => $this->server->verify_ssl,
            ],
            'mail.from' => [
                'address' => $this->server->default_from_email ?? config('mail.from.address'),
                'name' => $this->server->default_from_name ?? config('mail.from.name'),
            ]
        ]);
    }

    public function send(array $data)
    {
        $messageId = time() . '.' . uniqid() . '@' . parse_url($this->server->host, PHP_URL_HOST);
        
        Mail::mailer('smtp')->send(new DynamicEmail($data));
        
        return [
            'success' => true,
            'message_id' => $messageId,
            'provider' => 'smtp'
        ];
    }
}