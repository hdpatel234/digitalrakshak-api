<?php

namespace App\Services\Support\Drivers;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class UvdeskDriver extends AbstractSupportDriver
{
    public function createTicket(array $payload): array
    {
        return $this->request('post', $this->endpoint('create_ticket', '/ticket'), $payload);
    }

    public function getTicket(string $externalTicketId): array
    {
        $path = str_replace('{ticket_id}', $externalTicketId, $this->endpoint('get_ticket', '/ticket/{ticket_id}'));

        return $this->request('get', $path);
    }

    public function addReply(string $externalTicketId, array $payload): array
    {
        $path = str_replace('{ticket_id}', $externalTicketId, $this->endpoint('add_reply', '/ticket/{ticket_id}/replies'));

        return $this->request('post', $path, $payload);
    }

    public function closeTicket(string $externalTicketId, array $payload = []): array
    {
        $path = str_replace('{ticket_id}', $externalTicketId, $this->endpoint('close_ticket', '/ticket/{ticket_id}/close'));

        return $this->request('post', $path, $payload);
    }

    public function syncTicketStatus(string $externalTicketId): array
    {
        return $this->getTicket($externalTicketId);
    }

    protected function endpoint(string $key, string $default): string
    {
        return (string) $this->additionalConfig("endpoints.{$key}", $default);
    }

    protected function request(string $method, string $path, array $payload = []): array
    {
        $baseUrl = rtrim((string) $this->requireConfig('api_url'), '/');
        $timeout = (int) $this->additionalConfig('timeout', 30);

        $client = Http::timeout($timeout)->acceptJson();

        if (filled($this->supportConfig->api_token)) {
            $client = $client->withToken((string) $this->supportConfig->api_token);
        } elseif (filled($this->supportConfig->api_key)) {
            $client = $client->withHeaders([
                'X-API-KEY' => (string) $this->supportConfig->api_key,
            ]);
        }

        $response = $client->{$method}($baseUrl . $path, $payload);

        if ($response->failed()) {
            throw new RuntimeException('UVDesk API request failed: ' . $response->body());
        }

        return $response->json() ?? [];
    }
}
