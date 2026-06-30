<?php

namespace App\Services\Billing;

use App\Models\Client;
use App\Models\BillingConfig;
use App\Services\Billing\Drivers\AbstractBillingDriver;
use InvalidArgumentException;
use RuntimeException;

class BillingDriverFactory
{
    public function driver(Client $client, ?BillingConfig $billingConfig = null): AbstractBillingDriver
    {
        $billingConfig ??= $this->resolveBillingConfig($client);

        $driverClass = $this->resolveDriverClass($billingConfig);

        return new $driverClass($billingConfig);
    }

    protected function resolveBillingConfig(Client $client): BillingConfig
    {
        $platformId = (int) $client->default_billing_config_id;

        if ($platformId <= 0) {
            throw new RuntimeException("No default billing provider configured for client [{$client->id}].");
        }

        $platform = \App\Models\BillingPlatform::find($platformId);

        if (!$platform) {
            throw new RuntimeException("Billing platform ID [$platformId] not found.");
        }

        $billingConfig = BillingConfig::where('billing_platform_id', $platformId)
            ->where(function ($query) {
                $query->where('is_default', true)
                      ->orWhere('status', 'active');
            })
            ->first()
            ?? BillingConfig::where('billing_platform_id', $platformId)
                ->first();

        if (!$billingConfig instanceof BillingConfig) {
            throw new RuntimeException("No billing configuration found for billing platform [{$platformId}].");
        }

        $billingConfig->setRelation('billingPlatform', $platform);

        return $billingConfig;
    }

    protected function resolveDriverClass(BillingConfig $billingConfig): string
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

        $drivers = config('billing.drivers', []);

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
