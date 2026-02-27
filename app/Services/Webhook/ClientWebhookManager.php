<?php

namespace App\Services\Webhook;

use App\Models\Client;
use App\Models\ClientWebhook;
use App\Models\ClientWebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClientWebhookManager
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Dispatch webhook for event
     */
    public function dispatch(string $eventCode, array $payload, array $metadata = []): void
    {
        // Get all active webhooks subscribed to this event
        $webhooks = ClientWebhook::where('client_id', $this->client->id)
            ->where('is_active', true)
            ->whereJsonContains('events', $eventCode)
            ->get();

        foreach ($webhooks as $webhook) {
            $this->sendWebhook($webhook, $eventCode, $payload);
        }
    }

    /**
     * Send webhook to endpoint
     */
    protected function sendWebhook(ClientWebhook $webhook, string $eventCode, array $payload): void
    {
        // Create webhook log
        $log = ClientWebhookLog::create([
            'client_id' => $this->client->id,
            'webhook_id' => $webhook->id,
            'event_type' => $eventCode,
            'payload' => $payload,
            'status' => 'pending'
        ]);

        try {
            // Prepare payload with metadata
            $webhookPayload = [
                'event' => $eventCode,
                'timestamp' => now()->toIso8601String(),
                'client_id' => $this->client->id,
                'data' => $payload,
                'metadata' => [
                    'webhook_id' => $webhook->id,
                    'attempt' => 1
                ]
            ];

            // Generate signature if secret exists
            $headers = $webhook->headers ?? [];
            if ($webhook->webhook_secret) {
                $signature = $this->generateSignature($webhookPayload, $webhook->webhook_secret);
                $headers['X-Webhook-Signature'] = $signature;
            }

            // Send webhook
            $startTime = microtime(true);
            $response = Http::timeout($webhook->timeout_seconds)
                ->withHeaders($headers)
                ->{$webhook->format === 'xml' ? 'asXml' : 'asJson'}()
                ->post($webhook->webhook_url, $webhookPayload);

            $responseTime = round((microtime(true) - $startTime) * 1000);

            // Update log
            $log->update([
                'response_code' => $response->status(),
                'response_body' => substr($response->body(), 0, 5000), // Truncate long responses
                'response_time_ms' => $responseTime,
                'status' => $response->successful() ? 'success' : 'failed',
                'updated_at' => now()
            ]);

            // Update webhook stats
            if ($response->successful()) {
                $webhook->increment('total_success');
                $webhook->update([
                    'last_success_at' => now(),
                    'last_triggered_at' => now()
                ]);
            } else {
                $webhook->increment('total_failures');
                $webhook->update([
                    'last_failure_at' => now(),
                    'last_triggered_at' => now(),
                    'last_error' => "HTTP {$response->status()}: " . substr($response->body(), 0, 200)
                ]);

                // Handle retry logic
                if ($webhook->max_retries > 0) {
                    $this->scheduleRetry($log, $webhook);
                }
            }

        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'updated_at' => now()
            ]);

            $webhook->increment('total_failures');
            $webhook->update([
                'last_failure_at' => now(),
                'last_triggered_at' => now(),
                'last_error' => $e->getMessage()
            ]);

            Log::error("Webhook delivery failed", [
                'webhook_id' => $webhook->id,
                'event' => $eventCode,
                'error' => $e->getMessage()
            ]);

            // Handle retry logic
            if ($webhook->max_retries > 0) {
                $this->scheduleRetry($log, $webhook);
            }
        }
    }

    /**
     * Schedule webhook retry
     */
    protected function scheduleRetry(ClientWebhookLog $log, ClientWebhook $webhook): void
    {
        if ($log->attempt < $webhook->max_retries) {
            $nextRetry = now()->addSeconds($webhook->retry_delay_seconds * $log->attempt);
            
            $log->update([
                'status' => 'retrying',
                'next_retry_at' => $nextRetry
            ]);

            // Dispatch job for retry
            RetryWebhookJob::dispatch($log, $webhook)
                ->delay($nextRetry);
        }
    }

    /**
     * Generate webhook signature
     */
    protected function generateSignature(array $payload, string $secret): string
    {
        $payloadJson = json_encode($payload);
        return hash_hmac('sha256', $payloadJson, $secret);
    }

    /**
     * Test webhook endpoint
     */
    public function testWebhook(ClientWebhook $webhook): array
    {
        $testPayload = [
            'event' => 'webhook.test',
            'timestamp' => now()->toIso8601String(),
            'client_id' => $this->client->id,
            'data' => [
                'message' => 'This is a test webhook from the system',
                'webhook_id' => $webhook->id,
                'webhook_name' => $webhook->webhook_name
            ]
        ];

        try {
            $startTime = microtime(true);
            $response = Http::timeout($webhook->timeout_seconds)
                ->withHeaders($webhook->headers ?? [])
                ->post($webhook->webhook_url, $testPayload);
            $responseTime = round((microtime(true) - $startTime) * 1000);

            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'response' => substr($response->body(), 0, 1000),
                'response_time' => $responseTime,
                'error' => null
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'status_code' => null,
                'response' => null,
                'response_time' => null,
                'error' => $e->getMessage()
            ];
        }
    }
}