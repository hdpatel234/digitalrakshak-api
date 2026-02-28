<?php

namespace App\Services\Billing;

use App\Models\Client;
use App\Models\ClientBillingConfig;
use App\Services\Billing\Drivers\AbstractBillingDriver;
use App\Services\Billing\Drivers\InvoiceNinjaDriver;
use InvalidArgumentException;
use RuntimeException;

class BillingDriverFactory
{
    public function driver(Client $client, ?ClientBillingConfig $billingConfig = null): AbstractBillingDriver
    {
        $billingConfig ??= $this->resolveBillingConfig($client);

        $driverClass = $this->resolveDriverClass($billingConfig);

        return new $driverClass($billingConfig);
    }

    protected function resolveBillingConfig(Client $client): ClientBillingConfig
    {
        $client->loadMissing('defaultBillingConfig.billingPlatform', 'billingConfigs.billingPlatform');

        $billingConfig = $client->defaultBillingConfig
            ?? $client->billingConfigs->firstWhere(ClientBillingConfig::IS_DEFAULT, true)
            ?? $client->billingConfigs->firstWhere(ClientBillingConfig::STATUS, 'active')
            ?? $client->billingConfigs->first();

        if (!$billingConfig instanceof ClientBillingConfig) {
            throw new RuntimeException("No billing configuration found for client [{$client->id}].");
        }

        return $billingConfig;
    }

    protected function resolveDriverClass(ClientBillingConfig $billingConfig): string
    {
        $customDriverClass = data_get($billingConfig->additional_config, 'driver_class');
        if (is_string($customDriverClass) && class_exists($customDriverClass)) {
            if (is_subclass_of($customDriverClass, AbstractBillingDriver::class)) {
                return $customDriverClass;
            }

            throw new InvalidArgumentException("Configured driver_class [$customDriverClass] must extend " . AbstractBillingDriver::class . '.');
        }

        $platformCode = data_get($billingConfig, 'billingPlatform.platform_code');

        if (!is_string($platformCode) || $platformCode === '') {
            throw new RuntimeException("Billing platform code not configured for client billing config [{$billingConfig->id}].");
        }

        $drivers = config('billing.drivers', [
            'invoice_ninja' => InvoiceNinjaDriver::class,
        ]);

        $driverClass = $drivers[$platformCode] ?? null;

        if (!is_string($driverClass) || !class_exists($driverClass)) {
            throw new InvalidArgumentException("Unsupported billing platform code [$platformCode].");
        }

        if (!is_subclass_of($driverClass, AbstractBillingDriver::class)) {
            throw new InvalidArgumentException("Billing driver [$driverClass] must extend " . AbstractBillingDriver::class . '.');
        }

        return $driverClass;
    }
}

