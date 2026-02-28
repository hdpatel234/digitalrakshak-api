<?php

namespace App\Services\Support;

use App\Models\Client;
use App\Models\ClientSupportConfig;
use App\Services\Support\Drivers\AbstractSupportDriver;
use App\Services\Support\Drivers\UvdeskDriver;
use InvalidArgumentException;
use RuntimeException;

class SupportDriverFactory
{
    public function driver(Client $client, ?ClientSupportConfig $supportConfig = null): AbstractSupportDriver
    {
        $supportConfig ??= $this->resolveSupportConfig($client);

        $driverClass = $this->resolveDriverClass($supportConfig);

        return new $driverClass($supportConfig);
    }

    protected function resolveSupportConfig(Client $client): ClientSupportConfig
    {
        $client->loadMissing('defaultSupportConfig.supportPlatform', 'supportConfigs.supportPlatform');

        $supportConfig = $client->defaultSupportConfig
            ?? $client->supportConfigs->firstWhere(ClientSupportConfig::IS_DEFAULT, true)
            ?? $client->supportConfigs->firstWhere(ClientSupportConfig::STATUS, 'active')
            ?? $client->supportConfigs->first();

        if (!$supportConfig instanceof ClientSupportConfig) {
            throw new RuntimeException("No support configuration found for client [{$client->id}].");
        }

        return $supportConfig;
    }

    protected function resolveDriverClass(ClientSupportConfig $supportConfig): string
    {
        $customDriverClass = data_get($supportConfig->additional_config, 'driver_class');
        if (is_string($customDriverClass) && class_exists($customDriverClass)) {
            if (is_subclass_of($customDriverClass, AbstractSupportDriver::class)) {
                return $customDriverClass;
            }

            throw new InvalidArgumentException("Configured driver_class [$customDriverClass] must extend " . AbstractSupportDriver::class . '.');
        }

        $platformCode = data_get($supportConfig, 'supportPlatform.platform_code');

        if (!is_string($platformCode) || $platformCode === '') {
            throw new RuntimeException("Support platform code not configured for client support config [{$supportConfig->id}].");
        }

        $drivers = config('support.drivers', [
            'uvdesk' => UvdeskDriver::class,
        ]);

        $driverClass = $drivers[$platformCode] ?? null;

        if (!is_string($driverClass) || !class_exists($driverClass)) {
            throw new InvalidArgumentException("Unsupported support platform code [$platformCode].");
        }

        if (!is_subclass_of($driverClass, AbstractSupportDriver::class)) {
            throw new InvalidArgumentException("Support driver [$driverClass] must extend " . AbstractSupportDriver::class . '.');
        }

        return $driverClass;
    }
}
