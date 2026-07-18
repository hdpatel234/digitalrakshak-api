<?php

namespace App\Services\ApiService\Client;

use App\Services\BaseService;
use App\Repositories\ClientRepository;

class CompanySettingService extends BaseService
{
    public function __construct(
        protected ClientRepository $clientRepo
    ) {}

    public function getSettings(int $clientId): array
    {
        $client = $this->clientRepo->find($clientId);

        if (!$client) {
            throw new \Exception('Client not found.', 404);
        }

        return $client->toArray();
    }

    public function updateSettings(array $data, int $clientId): array
    {
        $client = $this->clientRepo->find($clientId);

        if (!$client) {
            throw new \Exception('Client not found.', 404);
        }

        $this->clientRepo->update($clientId, $data);

        return $this->clientRepo->find($clientId)->toArray();
    }
}
