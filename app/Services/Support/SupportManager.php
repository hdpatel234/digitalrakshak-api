<?php

namespace App\Services\Support;

use App\Models\Client;
use App\Models\SupportConfig;
use App\Services\Support\Drivers\AbstractSupportDriver;

class SupportManager
{
    public function __construct(protected SupportDriverFactory $factory)
    {
    }

    public function driver(Client $client, ?SupportConfig $supportConfig = null): AbstractSupportDriver
    {
        return $this->factory->driver($client, $supportConfig);
    }

    public function createTicket(Client $client, array $payload, ?SupportConfig $supportConfig = null): array
    {
        return $this->driver($client, $supportConfig)->createTicket($payload);
    }

    public function getTicket(Client $client, string $externalTicketId, ?SupportConfig $supportConfig = null): array
    {
        return $this->driver($client, $supportConfig)->getTicket($externalTicketId);
    }

    public function addReply(
        Client $client,
        string $externalTicketId,
        array $payload,
        ?SupportConfig $supportConfig = null
    ): array {
        return $this->driver($client, $supportConfig)->addReply($externalTicketId, $payload);
    }

    public function closeTicket(
        Client $client,
        string $externalTicketId,
        array $payload = [],
        ?SupportConfig $supportConfig = null
    ): array {
        return $this->driver($client, $supportConfig)->closeTicket($externalTicketId, $payload);
    }

    public function syncTicketStatus(
        Client $client,
        string $externalTicketId,
        ?SupportConfig $supportConfig = null
    ): array {
        return $this->driver($client, $supportConfig)->syncTicketStatus($externalTicketId);
    }
}
