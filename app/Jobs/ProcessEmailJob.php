<?php

namespace App\Jobs;

use App\Enums\EmailQueueStatus;
use App\Enums\EmailServerStatus;
use App\Models\EmailQueue;
use App\Models\EmailServer;
use App\Models\EmailLog;
use App\Services\Email\EmailDriverFactory;
use App\Services\Email\TemplateVariableRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $emailQueueId;
    public $timeout = 120;
    public $tries = 3;

    public function __construct(int $emailQueueId)
    {
        $this->emailQueueId = $emailQueueId;
    }

    public function handle(EmailDriverFactory $driverFactory)
    {
        $emailQueue = EmailQueue::query()
            ->with(['assignedServer.serverType', 'assignedServer.configurationValues.field', 'attachments', 'template'])
            ->find($this->emailQueueId);

        if (!$emailQueue) {
            return;
        }

        if (!in_array((string) $emailQueue->status, [
            EmailQueueStatus::PENDING->value,
            EmailQueueStatus::PROCESSING->value,
        ], true)) {
            return;
        }

        $currentAttempts = ((int) $emailQueue->attempts) + 1;

        $emailQueue->update([
            'status' => EmailQueueStatus::PROCESSING->value,
            'attempts' => $currentAttempts,
            'last_attempt_at' => now(),
        ]);

        try {
            $emailQueue->refresh();
            $emailQueue->loadMissing(['assignedServer.serverType', 'assignedServer.configurationValues.field', 'attachments', 'template']);

            if (empty($emailQueue->to_email)) {
                throw new \RuntimeException('Queue email is missing to_email.');
            }

            if ($emailQueue->template_id && $emailQueue->template) {
                $variables = is_array($emailQueue->variables) ? $emailQueue->variables : [];
                $renderer = app(TemplateVariableRenderer::class);
                $systemVars = [
                    'app_name' => config('app.name'),
                    'app_url' => rtrim((string) config('app.url'), '/'),
                    'current_year' => now()->year,
                    'current_date' => now()->format('Y-m-d'),
                ];
                
                if ($emailQueue->template->subject) {
                    $emailQueue->subject = (string) $renderer->render($emailQueue->template->subject, $variables, $systemVars);
                }
                if ($emailQueue->template->body_html) {
                    $emailQueue->body_html = (string) $renderer->render($emailQueue->template->body_html, $variables, $systemVars);
                }
                if ($emailQueue->template->body_text) {
                    $emailQueue->body_text = (string) $renderer->render($emailQueue->template->body_text, $variables, $systemVars);
                }
            }

            $server = $emailQueue->assignedServer;

            if (!$server || $server->status !== EmailServerStatus::ACTIVE->value) {
                $server = $this->findAlternativeServer();

                if (!$server) {
                    throw new \Exception('No active email server available');
                }

                $emailQueue->update(['assigned_server_id' => $server->id]);
            }

            $driver = $driverFactory->driver($server);

            $attachments = $emailQueue->attachments
                ->map(fn ($attachment) => [
                    'filename' => $attachment->filename,
                    'file_path' => $attachment->file_path,
                    'file_size' => $attachment->file_size,
                    'mime_type' => $attachment->mime_type,
                    'cid' => $attachment->cid,
                    'is_inline' => (bool) $attachment->is_inline,
                ])
                ->values()
                ->all();

            $emailData = [
                'to' => [
                    'email' => $emailQueue->to_email,
                    'name' => $emailQueue->to_name,
                ],
                'cc' => is_array($emailQueue->cc) ? $emailQueue->cc : [],
                'bcc' => is_array($emailQueue->bcc) ? $emailQueue->bcc : [],
                'reply_to' => $emailQueue->reply_to,
                'subject' => $emailQueue->subject,
                'html' => $emailQueue->body_html,
                'text' => $emailQueue->body_text,
                'attachments' => $attachments,
                'metadata' => [
                    'email_uid' => $emailQueue->email_uid,
                    'client_id' => $emailQueue->client_id,
                    'candidate_id' => $emailQueue->candidate_id,
                    'order_id' => $emailQueue->order_id,
                ],
            ];

            $result = $driver->send($emailData);
            $sentAt = now();

            $emailQueue->update([
                'status' => EmailQueueStatus::SENT->value,
                'sent_at' => $sentAt,
                'message_id' => $result['message_id'] ?? null,
                'provider_response' => $result,
            ]);

            EmailLog::create([
                'email_queue_id' => $emailQueue->id,
                'server_id' => $server->id,
                'status' => EmailQueueStatus::SENT->value,
                'provider_response' => $result,
            ]);

            $server->increment('success_count');
            $server->update(['last_used_at' => now()]);
        } catch (\Exception $e) {
            Log::error('Email processing failed', [
                'email_queue_id' => $emailQueue->id,
                'email_uid' => $emailQueue->email_uid,
                'error' => $e->getMessage(),
            ]);

            $maxAttempts = max(1, (int) $emailQueue->max_attempts);
            $hasAttemptsLeft = $currentAttempts < $maxAttempts;

            $emailQueue->update([
                'status' => $hasAttemptsLeft
                    ? EmailQueueStatus::PENDING->value
                    : EmailQueueStatus::FAILED->value,
                'error_message' => $e->getMessage(),
            ]);

            EmailLog::create([
                'email_queue_id' => $emailQueue->id,
                'server_id' => $emailQueue->assigned_server_id,
                'status' => EmailQueueStatus::FAILED->value,
                'error_message' => $e->getMessage(),
            ]);

            $emailQueue->loadMissing('assignedServer');
            if ($emailQueue->assignedServer) {
                $emailQueue->assignedServer->increment('failure_count');
            }

            if ($hasAttemptsLeft) {
                $this->release(60 * $currentAttempts);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Find alternative server in same group
     */
    protected function findAlternativeServer(): ?EmailServer
    {
        return EmailServer::query()
            ->where('status', EmailServerStatus::ACTIVE->value)
            ->orderBy('priority')
            ->first();
    }
}
