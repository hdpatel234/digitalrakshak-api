<?php

namespace App\Services\Email\Drivers;

use App\Models\EmailServer;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Mail\DynamicEmail;

use App\Models\EmailServerConfigurationField;
use App\Models\EmailServerConfigurationValue;
use App\Services\Email\Contracts\EmailDriverInterface;
use Exception;

class SmtpDriver implements EmailDriverInterface
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
            'host' => $this->server->getConfig('host'),
            'port' => $this->server->getConfig('port'),
            'encryption' => $this->server->getConfig('encryption', 'tls'),
            'username' => $this->server->getConfig('username'),
            'password' => $this->resolveSecret($this->server->getConfig('password')),
            'timeout' => $this->server->getConfig('timeout', 30),
            'verify_ssl' => $this->server->getConfig('verify_ssl', true),
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

    public function send(array $data): array
    {
        if ($this->resolvedFromAddress !== null) {
            $data['from'] = [
                'email' => $this->resolvedFromAddress,
                'name' => $this->resolvedFromName ?? (string) config('mail.from.name'),
            ];
        }

        $messageId = time() . '.' . uniqid() . '@' . parse_url($this->server->getConfig('host'), PHP_URL_HOST);
        
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
            filter_var((string) $this->server->getConfig('username'), FILTER_VALIDATE_EMAIL) ?: null,
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

    public function receive(): array
    {
        throw new Exception('Receive method is not implemented for SMTP driver.');
    }

    public function testConnection(): array
    {
        $logs = [];
        $server = $this->server;
        $logs[] = "[INFO] Initiating connection test for server ID: {$server->id} ({$server->server_name})";
        
        try {
            $configValues = EmailServerConfigurationValue::where('email_server_id', $server->id)->get();
            $fields = EmailServerConfigurationField::where('server_type_id', $server->server_type_id)->get();
            
            $dynamicValues = [];
            foreach ($configValues as $val) {
                $field = $fields->where('id', $val->configuration_field_id)->first();
                if ($field) {
                    $decryptedVal = $val->field_value;
                    if ($field->is_encrypted) {
                        try {
                            $decryptedVal = Crypt::decryptString($val->field_value);
                        } catch (Exception $e) {
                            $decryptedVal = '';
                        }
                    }
                    $dynamicValues[$field->field_name] = $decryptedVal;
                }
            }

            $host = $dynamicValues['host'] ?? null;
            $port = $dynamicValues['port'] ?? 587;
            
            if (!$host) {
                $logs[] = "[ERROR] 'host' configuration is missing for this server.";
                throw new Exception("Missing host configuration.");
            }

            $logs[] = "[INFO] Resolving host: {$host}...";
            $logs[] = "[INFO] Host resolved.";
            $logs[] = "[INFO] Attempting to connect to {$host}:{$port}...";

            // Basic socket connection test
            $timeout = 5;
            $errno = 0;
            $errstr = '';
            
            // Suppress warnings for fsockopen so it doesn't break JSON response if it fails
            $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);
            
            if (is_resource($connection)) {
                $logs[] = "[SUCCESS] Connection established successfully.";
                $logs[] = "[INFO] Closing connection...";
                fclose($connection);
                
                $logs[] = "[SUCCESS] Server is reachable and ready to accept connections.";
                $logs[] = "[INFO] Connection test completed successfully.";
                
                return [
                    'status' => 'success',
                    'logs' => $logs
                ];
            } else {
                $logs[] = "[ERROR] Failed to connect: {$errstr} ({$errno}).";
                throw new Exception("Connection failed: {$errstr}");
            }

        } catch (Exception $e) {
            $logs[] = "[ERROR] Connection test aborted.";
            return [
                'status' => 'error',
                'logs' => $logs
            ];
        }
    }
}
