<?php

namespace App\Services\Support\Drivers;

use App\Models\SupportConfig;
use InvalidArgumentException;

abstract class AbstractSupportDriver
{
    public function __construct(protected SupportConfig $supportConfig)
    {
    }

    abstract public function createTicket(array $payload): array;

    abstract public function getTicket(string $externalTicketId): array;

    abstract public function addReply(string $externalTicketId, array $payload): array;

    abstract public function closeTicket(string $externalTicketId, array $payload = []): array;

    abstract public function syncTicketStatus(string $externalTicketId): array;

    protected function requireConfig(string $key): mixed
    {
        $value = $this->supportConfig->{$key} ?? null;

        if (blank($value)) {
            throw new InvalidArgumentException("Support configuration value [$key] is required.");
        }

        return $value;
    }

    protected function additionalConfig(string $key, mixed $default = null): mixed
    {
        return data_get($this->supportConfig->additional_config, $key, $default);
    }
}
