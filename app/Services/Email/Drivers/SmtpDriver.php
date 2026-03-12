<?php

namespace App\Services\Email\Drivers;

use App\Models\EmailServer;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Mail\DynamicEmail;

class SmtpDriver
{
    protected $server;
    protected ?string $resolvedFromAddress = null;
    protected ?string $resolvedFromName = null;

    public function __construct(EmailServer $server)
    {
        $this->server = $server;
        $this->configureSmtp();
    }

    protected function configureSmtp()
    {
        $this->resolvedFromAddress = $this->resolveFromAddress();
        $this->resolvedFromName = $this->resolveFromName();

        $smtpConfig = [
            'transport' => 'smtp',
            'host' => $this->server->host,
            'port' => $this->server->port,
            'encryption' => $this->server->encryption,
            'username' => $this->server->username,
            'password' => $this->resolveSecret($this->server->password),
            'timeout' => $this->server->timeout,
            'verify_ssl' => $this->server->verify_ssl,
        ];

        $mailConfig = [
            'mail.mailers.smtp' => array_filter(
                $smtpConfig,
                static fn ($value) => $value !== null && $value !== ''
            ),
        ];

        if ($this->resolvedFromAddress !== null) {
            $mailConfig['mail.from'] = [
                'address' => $this->resolvedFromAddress,
                'name' => $this->resolvedFromName ?? (string) config('mail.from.name'),
            ];
        }

        config([
            ...$mailConfig,
        ]);
    }

    public function send(array $data)
    {
        if ($this->resolvedFromAddress !== null) {
            $data['from'] = [
                'email' => $this->resolvedFromAddress,
                'name' => $this->resolvedFromName ?? (string) config('mail.from.name'),
            ];
        }

        $messageId = time() . '.' . uniqid() . '@' . parse_url($this->server->host, PHP_URL_HOST);
        
        Mail::mailer('smtp')->send(new DynamicEmail($data));
        
        return [
            'success' => true,
            'message_id' => $messageId,
            'provider' => 'smtp'
        ];
    }

    protected function resolveSecret(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            // Backward compatibility for existing plain-text values.
            return $value;
        }
    }

    protected function resolveFromAddress(): ?string
    {
        $candidates = [
            $this->server->default_from_email,
            filter_var((string) $this->server->username, FILTER_VALIDATE_EMAIL) ?: null,
            config('mail.from.address'),
        ];

        foreach ($candidates as $candidate) {
            $address = trim((string) $candidate);
            if ($address === '' || !$this->isValidEmail($address)) {
                continue;
            }

            $domain = strtolower((string) substr(strrchr($address, '@') ?: '', 1));
            if ($domain === 'example.com') {
                continue;
            }

            return $address;
        }

        return null;
    }

    protected function resolveFromName(): ?string
    {
        $name = trim((string) ($this->server->default_from_name ?? ''));
        if ($name !== '') {
            return $name;
        }

        $fallback = trim((string) config('mail.from.name'));

        return $fallback !== '' ? $fallback : null;
    }

    protected function isValidEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
