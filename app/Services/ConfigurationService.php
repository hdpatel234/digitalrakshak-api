<?php

namespace App\Services;

use App\Enums\ConfigurationKey;
use App\Repositories\ConfigurationRepository;

class ConfigurationService extends BaseService
{
    public function __construct(ConfigurationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function configKey()
    {
        return $this->repository->configKey();
    }

    public function configValue()
    {
        return $this->repository->configValue();
    }

    public function description()
    {
        return $this->repository->description();
    }

    public function createdBy()
    {
        return $this->repository->createdBy();
    }

    public function updatedBy()
    {
        return $this->repository->updatedBy();
    }

    public function getValue(ConfigurationKey|string $key, mixed $default = null): mixed
    {
        $configKey = $key instanceof ConfigurationKey ? $key->value : $key;

        $config = $this->query()
            ->where($this->configKey(), $configKey)
            ->first();

        if (!$config) {
            return $default;
        }

        $value = $config->{$this->configValue()};

        return $value !== null ? $value : $default;
    }

    public function getIntValue(ConfigurationKey|string $key, int $default = 0): int
    {
        $value = $this->getValue($key, $default);

        if (is_numeric($value)) {
            return (int) $value;
        }

        return $default;
    }

    public function getStringValue(ConfigurationKey|string $key, string $default = ''): string
    {
        $value = $this->getValue($key, $default);
        $value = is_string($value) ? trim($value) : '';

        return $value !== '' ? $value : $default;
    }
}
