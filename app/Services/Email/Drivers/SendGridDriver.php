<?php

namespace App\Services\Email\Drivers;

use App\Models\EmailServer;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use SendGrid\Mail\Mail;
use SendGrid;

use App\Services\Email\Contracts\EmailDriverInterface;
use Exception;

class SendGridDriver implements EmailDriverInterface
{
    protected $server;
    protected $sendgrid;

    public function __construct(EmailServer $server)
    {
        $this->server = $server;
        $this->sendgrid = new SendGrid($this->resolveSecret($server->getConfig('api_key')));
    }

    public function send(array $data): array
    {
        $email = new Mail();
        
        // Set from
        $fromEmail = $this->server->default_from_email ?? config('mail.from.address');
        $fromName = $this->server->default_from_name ?? config('mail.from.name');
        $email->setFrom($fromEmail, $fromName);
        
        // Set reply to
        if ($data['reply_to'] ?? null) {
            $email->setReplyTo($data['reply_to']);
        }
        
        // Set subject
        $email->setSubject($data['subject']);
        
        // Set recipients
        $email->addTo($data['to']['email'], $data['to']['name'] ?? '');
        
        // Set CC
        if ($data['cc'] ?? null) {
            foreach ($data['cc'] as $cc) {
                $email->addCc($cc['email'], $cc['name'] ?? '');
            }
        }
        
        // Set BCC
        if ($data['bcc'] ?? null) {
            foreach ($data['bcc'] as $bcc) {
                $email->addBcc($bcc['email'], $bcc['name'] ?? '');
            }
        }
        
        // Set content
        if ($data['html'] ?? null) {
            $email->addContent('text/html', $data['html']);
        }
        if ($data['text'] ?? null) {
            $email->addContent('text/plain', $data['text']);
        }
        
        // Add attachments
        if ($data['attachments'] ?? null) {
            foreach ($data['attachments'] as $attachment) {
                $email->addAttachment(
                    base64_encode(file_get_contents($attachment['file_path'])),
                    $attachment['mime_type'],
                    $attachment['filename'],
                    $attachment['cid'] ?? null,
                    $attachment['is_inline'] ? 'inline' : 'attachment'
                );
            }
        }
        
        // Add custom headers
        $email->addCustomArg('email_uid', $data['metadata']['email_uid']);
        $email->addCustomArg('client_id', (string)($data['metadata']['client_id'] ?? ''));
        
        // Send
        $response = $this->sendgrid->send($email);
        
        if ($response->statusCode() >= 400) {
            throw new \Exception('SendGrid error: ' . $response->body());
        }
        
        return [
            'success' => true,
            'message_id' => $response->headers()['X-Message-Id'] ?? null,
            'provider' => 'sendgrid',
            'status_code' => $response->statusCode()
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

    public function receive(): array
    {
        throw new Exception('Receive method is not implemented for SendGrid driver.');
    }

    public function testConnection(): array
    {
        $server = $this->server;
        $typeName = $server->serverType ? $server->serverType->type_name : 'Unknown';
        
        $logs = [
            "[INFO] Initiating connection test for server ID: {$server->id} ({$server->server_name})",
            "[WARNING] No specific tester implemented for server type '{$typeName}'.",
            "[INFO] Using DummyTester...",
            "[SUCCESS] Dummy connection test successful.",
            "[INFO] Connection test completed successfully."
        ];

        return [
            'status' => 'success',
            'logs' => $logs
        ];
    }
}
