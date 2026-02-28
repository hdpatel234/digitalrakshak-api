<?php

namespace App\Services\Document\Drivers;

use App\Models\ClientDocumentConfig;
use InvalidArgumentException;

abstract class AbstractDocumentDriver
{
    public function __construct(protected ClientDocumentConfig $documentConfig)
    {
    }

    abstract public function uploadDocument(string $path, string $contents, array $options = []): array;

    abstract public function getDocument(string $path): array;

    abstract public function deleteDocument(string $path): array;

    abstract public function createShareLink(string $path, array $options = []): array;

    protected function requireConfig(string $key): mixed
    {
        $value = $this->documentConfig->{$key} ?? null;

        if (blank($value)) {
            throw new InvalidArgumentException("Document configuration value [$key] is required.");
        }

        return $value;
    }

    protected function additionalConfig(string $key, mixed $default = null): mixed
    {
        return data_get($this->documentConfig->additional_config, $key, $default);
    }
}
