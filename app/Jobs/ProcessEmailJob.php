<?php

namespace App\Jobs;

use App\Models\EmailQueue;
use App\Models\EmailServer;
use App\Models\EmailLog;
use App\Services\Email\EmailDriverFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailQueue;
    public $timeout = 120;
    public $tries = 3;

    public function __construct(EmailQueue $emailQueue)
    {
        $this->emailQueue = $emailQueue;
    }

    public function handle(EmailDriverFactory $driverFactory)
    {
        // Update status to processing
        $this->emailQueue->update([
            'status' => 'processing',
            'attempts' => $this->emailQueue->attempts + 1,
            'last_attempt_at' => now()
        ]);

        try {
            // Get assigned server
            $server = $this->emailQueue->assignedServer;
            
            if (!$server || $server->status !== 'active') {
                // Try to find alternative server
                $server = $this->findAlternativeServer();
                
                if (!$server) {
                    throw new \Exception('No active email server available');
                }
                
                $this->emailQueue->update(['assigned_server_id' => $server->id]);
            }

            // Create driver instance
            $driver = $driverFactory->driver($server);

            // Prepare email data
            $emailData = [
                'to' => [
                    'email' => $this->emailQueue->to_email,
                    'name' => $this->emailQueue->to_name
                ],
                'cc' => $this->emailQueue->cc,
                'bcc' => $this->emailQueue->bcc,
                'reply_to' => $this->emailQueue->reply_to,
                'subject' => $this->emailQueue->subject,
                'html' => $this->emailQueue->body_html,
                'text' => $this->emailQueue->body_text,
                'attachments' => $this->emailQueue->attachments,
                'metadata' => [
                    'email_uid' => $this->emailQueue->email_uid,
                    'client_id' => $this->emailQueue->client_id,
                    'candidate_id' => $this->emailQueue->candidate_id,
                    'order_id' => $this->emailQueue->order_id
                ]
            ];

            // Send email
            $result = $driver->send($emailData);

            // Update queue status
            $this->emailQueue->update([
                'status' => 'sent',
                'sent_at' => now(),
                'message_id' => $result['message_id'] ?? null,
                'provider_response' => $result
            ]);

            // Create log entry
            EmailLog::create([
                'email_queue_id' => $this->emailQueue->id,
                'email_uid' => $this->emailQueue->email_uid,
                'to_email' => $this->emailQueue->to_email,
                'subject' => $this->emailQueue->subject,
                'server_id' => $server->id,
                'message_id' => $result['message_id'] ?? null,
                'status' => 'sent',
                'provider_response' => $result,
                'sent_at' => now()
            ]);

            // Update server stats
            $server->increment('success_count');
            $server->update(['last_used_at' => now()]);

        } catch (\Exception $e) {
            // Log failure
            Log::error('Email processing failed', [
                'email_uid' => $this->emailQueue->email_uid,
                'error' => $e->getMessage()
            ]);

            // Update queue with error
            $this->emailQueue->update([
                'status' => $this->attempts >= $this->tries ? 'failed' : 'pending',
                'error_message' => $e->getMessage()
            ]);

            // Create log entry
            EmailLog::create([
                'email_queue_id' => $this->emailQueue->id,
                'email_uid' => $this->emailQueue->email_uid,
                'to_email' => $this->emailQueue->to_email,
                'subject' => $this->emailQueue->subject,
                'server_id' => $this->emailQueue->assigned_server_id,
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            // Update server stats if server exists
            if ($this->emailQueue->assignedServer) {
                $this->emailQueue->assignedServer->increment('failure_count');
            }

            // Requeue if attempts left
            if ($this->attempts < $this->tries) {
                $this->release(60 * $this->attempts); // Exponential backoff
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
        $currentServer = $this->emailQueue->assignedServer;
        
        if (!$currentServer) {
            return EmailServer::where('status', 'active')
                ->orderBy('priority')
                ->first();
        }

        return EmailServer::where('server_group', $currentServer->server_group)
            ->where('status', 'active')
            ->where('id', '!=', $currentServer->id)
            ->orderBy('priority')
            ->first();
    }
}