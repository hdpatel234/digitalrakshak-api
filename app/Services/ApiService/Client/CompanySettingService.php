<?php

namespace App\Services\ApiService\Client;

use App\Services\BaseService;
use App\Services\ClientService;

class CompanySettingService extends BaseService
{
    public function __construct(
        protected ClientService $clientService
    ) {}

    public function getSettings(int $clientId): array
    {
        $client = $this->clientService->query()->where('id', $clientId)->first();

        if (!$client) {
            throw new \Exception('Client not found.', 404);
        }

        return $client->toArray();
    }

    public function updateSettings(array $data, int $clientId): array
    {
        $client = $this->clientService->query()->where('id', $clientId)->first();

        if (!$client) {
            throw new \Exception('Client not found.', 404);
        }

        $client->update($data);

        return $client->fresh()->toArray();
    }
}
