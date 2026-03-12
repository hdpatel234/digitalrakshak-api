<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DynamicEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected array $payload)
    {
    }

    public function build(): self
    {
        $message = $this->subject((string) ($this->payload['subject'] ?? ''));
        $this->applyRecipients($message);
        $this->applyFrom($message);

        if (!empty($this->payload['reply_to'])) {
            $replyTo = $this->payload['reply_to'];

            if (is_array($replyTo)) {
                $message->replyTo(
                    (string) ($replyTo['email'] ?? ''),
                    (string) ($replyTo['name'] ?? '')
                );
            } else {
                $message->replyTo((string) $replyTo);
            }
        }

        if (!empty($this->payload['html'])) {
            $message->html((string) $this->payload['html']);
        } elseif (!empty($this->payload['text'])) {
            $message->text('emails.plain', ['content' => (string) $this->payload['text']]);
        }

        return $message;
    }

    protected function applyFrom(self $message): void
    {
        $from = $this->payload['from'] ?? null;

        if (is_array($from) && !empty($from['email'])) {
            $message->from((string) $from['email'], (string) ($from['name'] ?? ''));
            return;
        }

        if (is_string($from) && trim($from) !== '') {
            $message->from(trim($from));
        }
    }

    protected function applyRecipients(self $message): void
    {
        $to = $this->payload['to'] ?? null;
        if (is_array($to) && !empty($to['email'])) {
            $message->to((string) $to['email'], (string) ($to['name'] ?? ''));
        } elseif (is_string($to) && trim($to) !== '') {
            $message->to(trim($to));
        }

        foreach ($this->normalizeRecipientList($this->payload['cc'] ?? []) as $cc) {
            $message->cc($cc['email'], $cc['name']);
        }

        foreach ($this->normalizeRecipientList($this->payload['bcc'] ?? []) as $bcc) {
            $message->bcc($bcc['email'], $bcc['name']);
        }
    }

    /**
     * @return array<int, array{email: string, name: string}>
     */
    protected function normalizeRecipientList(mixed $value): array
    {
        if (is_string($value)) {
            $value = array_map('trim', explode(',', $value));
        }

        if (!is_array($value)) {
            return [];
        }

        $normalized = [];

        foreach ($value as $item) {
            if (is_string($item) && trim($item) !== '') {
                $normalized[] = ['email' => trim($item), 'name' => ''];
                continue;
            }

            if (is_array($item) && !empty($item['email'])) {
                $normalized[] = [
                    'email' => trim((string) $item['email']),
                    'name' => trim((string) ($item['name'] ?? '')),
                ];
            }
        }

        return $normalized;
    }
}
