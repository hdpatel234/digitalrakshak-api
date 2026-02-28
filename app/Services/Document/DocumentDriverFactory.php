<?php

namespace App\Services\Document;

use App\Models\Client;
use App\Models\ClientDocumentConfig;
use App\Services\Document\Drivers\AbstractDocumentDriver;
use App\Services\Document\Drivers\NextcloudDriver;
use InvalidArgumentException;
use RuntimeException;

class DocumentDriverFactory
{
    public function driver(Client $client, ?ClientDocumentConfig $documentConfig = null): AbstractDocumentDriver
    {
        $documentConfig ??= $this->resolveDocumentConfig($client);

        $driverClass = $this->resolveDriverClass($documentConfig);

        return new $driverClass($documentConfig);
    }

    protected function resolveDocumentConfig(Client $client): ClientDocumentConfig
    {
        $client->loadMissing('defaultDocumentConfig.documentPlatform', 'documentConfigs.documentPlatform');

        $documentConfig = $client->defaultDocumentConfig
            ?? $client->documentConfigs->firstWhere(ClientDocumentConfig::IS_DEFAULT, true)
            ?? $client->documentConfigs->firstWhere(ClientDocumentConfig::STATUS, 'active')
            ?? $client->documentConfigs->first();

        if (!$documentConfig instanceof ClientDocumentConfig) {
            throw new RuntimeException("No document configuration found for client [{$client->id}].");
        }

        return $documentConfig;
    }

    protected function resolveDriverClass(ClientDocumentConfig $documentConfig): string
    {
        $customDriverClass = data_get($documentConfig->additional_config, 'driver_class');
        if (is_string($customDriverClass) && class_exists($customDriverClass)) {
            if (is_subclass_of($customDriverClass, AbstractDocumentDriver::class)) {
                return $customDriverClass;
            }

            throw new InvalidArgumentException("Configured driver_class [$customDriverClass] must extend " . AbstractDocumentDriver::class . '.');
        }

        $platformCode = data_get($documentConfig, 'documentPlatform.platform_code');

        if (!is_string($platformCode) || $platformCode === '') {
            throw new RuntimeException("Document platform code not configured for client document config [{$documentConfig->id}].");
        }

        $drivers = config('document.drivers', [
            'nextcloud' => NextcloudDriver::class,
        ]);

        $driverClass = $drivers[$platformCode] ?? null;

        if (!is_string($driverClass) || !class_exists($driverClass)) {
            throw new InvalidArgumentException("Unsupported document platform code [$platformCode].");
        }

        if (!is_subclass_of($driverClass, AbstractDocumentDriver::class)) {
            throw new InvalidArgumentException("Document driver [$driverClass] must extend " . AbstractDocumentDriver::class . '.');
        }

        return $driverClass;
    }
}
