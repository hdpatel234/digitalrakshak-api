<?php

namespace App\Jobs;

use App\Models\ClientWebhook;
use App\Models\ClientWebhookLog;
use App\Services\ClientWebhookLogService;
use App\Services\ClientWebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RetryWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ClientWebhookLog $log;
    public ClientWebhook $webhook;

    /**
     * Create a new job instance.
     */
    public function __construct(ClientWebhookLog $log, ClientWebhook $webhook)
    {
        $this->log = $log;
        $this->webhook = $webhook;
    }

    /**
     * Execute the job.
     */
    public function handle(
        ClientWebhookService $webhookService,
        ClientWebhookLogService $webhookLogService
    ): void {
        // Increment attempt
        $attempt = $this->log->attempt + 1;
        
        $webhookLogService->update($this->log->id, [
            'attempt' => $attempt,
            'status' => 'processing'
        ]);

        try {
            // Prepare payload with metadata
            $webhookPayload = [
                'event' => $this->log->event_type,
                'timestamp' => now()->toIso8601String(),
                'client_id' => $this->log->client_id,
                'data' => $this->log->payload,
                'metadata' => [
                    'webhook_id' => $this->webhook->id,
                    'attempt' => $attempt
                ]
            ];

            $headers = $this->webhook->headers ?? [];
            if ($this->webhook->webhook_secret) {
                $payloadJson = json_encode($webhookPayload);
                $signature = hash_hmac('sha256', $payloadJson, $this->webhook->webhook_secret);
                $headers['X-Webhook-Signature'] = $signature;
            }

            $startTime = microtime(true);
            $response = Http::timeout($this->webhook->timeout_seconds)
                ->withHeaders($headers)
                ->{$this->webhook->format === 'xml' ? 'asXml' : 'asJson'}()
                ->post($this->webhook->webhook_url, $webhookPayload);

            $responseTime = round((microtime(true) - $startTime) * 1000);

            $webhookLogService->update($this->log->id, [
                'response_code' => $response->status(),
                'response_body' => substr($response->body(), 0, 5000), // Truncate long responses
                'response_time_ms' => $responseTime,
                'status' => $response->successful() ? 'success' : 'failed',
                'updated_at' => now()
            ]);

            if ($response->successful()) {
                $webhookService->update($this->webhook->id, [
                    'total_success' => $this->webhook->total_success + 1,
                    'last_success_at' => now(),
                    'last_triggered_at' => now()
                ]);
            } else {
                $webhookService->update($this->webhook->id, [
                    'total_failures' => $this->webhook->total_failures + 1,
                    'last_failure_at' => now(),
                    'last_triggered_at' => now(),
                    'last_error' => "HTTP {$response->status()}: " . substr($response->body(), 0, 200)
                ]);

                $this->scheduleRetry($webhookLogService, $attempt);
            }

        } catch (\Exception $e) {
            $webhookLogService->update($this->log->id, [
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'updated_at' => now()
            ]);

            $webhookService->update($this->webhook->id, [
                'total_failures' => $this->webhook->total_failures + 1,
                'last_failure_at' => now(),
                'last_triggered_at' => now(),
                'last_error' => $e->getMessage()
            ]);

            Log::error("Webhook delivery retry failed", [
                'webhook_id' => $this->webhook->id,
                'event' => $this->log->event_type,
                'error' => $e->getMessage()
            ]);

            $this->scheduleRetry($webhookLogService, $attempt);
        }
    }

    /**
     * Schedule webhook retry
     */
    protected function scheduleRetry(ClientWebhookLogService $webhookLogService, int $currentAttempt): void
    {
        if ($currentAttempt < $this->webhook->max_retries) {
            $nextRetry = now()->addSeconds($this->webhook->retry_delay_seconds * $currentAttempt);
            
            $webhookLogService->update($this->log->id, [
                'status' => 'retrying',
                'next_retry_at' => $nextRetry
            ]);

            // Dispatch job for another retry
            self::dispatch($this->log, $this->webhook)
                ->delay($nextRetry);
        }
    }
}
