<?php

namespace App\Services\Webhook;

use App\Models\Client;
use Illuminate\Support\Facades\Log;
use Throwable;

class ClientWebhookDispatcher
{
    public const EVENT_CANDIDATE_CREATED = 'candidate.created';
    public const EVENT_CANDIDATE_IMPORT_QUEUED = 'candidate.import.queued';

    public function dispatchForClient(int $clientId, string $eventCode, array $payload = [], array $metadata = []): void
    {
        if ($clientId <= 0 || trim($eventCode) === '') {
            return;
        }

        $client = Client::query()->find($clientId);
        if (!$client) {
            Log::warning('Webhook dispatch skipped: client not found.', [
                'client_id' => $clientId,
                'event' => $eventCode,
            ]);
            return;
        }

        try {
            (new ClientWebhookManager($client))->dispatch($eventCode, $payload, $metadata);
        } catch (Throwable $e) {
            Log::error('Webhook dispatch failed.', [
                'client_id' => $clientId,
                'event' => $eventCode,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
