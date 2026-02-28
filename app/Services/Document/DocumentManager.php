<?php

namespace App\Services\Document;

use App\Models\Client;
use App\Models\ClientDocumentConfig;
use App\Services\Document\Drivers\AbstractDocumentDriver;

class DocumentManager
{
    public function __construct(protected DocumentDriverFactory $factory)
    {
    }

    public function driver(Client $client, ?ClientDocumentConfig $documentConfig = null): AbstractDocumentDriver
    {
        return $this->factory->driver($client, $documentConfig);
    }

    public function uploadDocument(
        Client $client,
        string $path,
        string $contents,
        array $options = [],
        ?ClientDocumentConfig $documentConfig = null
    ): array {
        return $this->driver($client, $documentConfig)->uploadDocument($path, $contents, $options);
    }

    public function getDocument(
        Client $client,
        string $path,
        ?ClientDocumentConfig $documentConfig = null
    ): array {
        return $this->driver($client, $documentConfig)->getDocument($path);
    }

    public function deleteDocument(
        Client $client,
        string $path,
        ?ClientDocumentConfig $documentConfig = null
    ): array {
        return $this->driver($client, $documentConfig)->deleteDocument($path);
    }

    public function createShareLink(
        Client $client,
        string $path,
        array $options = [],
        ?ClientDocumentConfig $documentConfig = null
    ): array {
        return $this->driver($client, $documentConfig)->createShareLink($path, $options);
    }
}
