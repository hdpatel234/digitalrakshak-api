<?php

namespace App\Services;

use App\Repositories\UserConfigDefinitionRepository;
use App\Repositories\UserConfigValueRepository;

class UserConfigService
{
    protected UserConfigDefinitionRepository $definitionRepository;
    protected UserConfigValueRepository $valueRepository;

    public function __construct(
        UserConfigDefinitionRepository $definitionRepository,
        UserConfigValueRepository $valueRepository
    ) {
        $this->definitionRepository = $definitionRepository;
        $this->valueRepository = $valueRepository;
    }

    public function getResolvedConfigs(int|string $userId): array
    {
        return $this->definitionRepository->getResolvedConfigsForUser($userId);
    }

    public function updateConfigs(int|string $userId, mixed $payload): array
    {
        if (!is_array($payload)) {
            return $this->getResolvedConfigs($userId);
        }

        $flatConfigs = $this->normalizeConfigPayload($payload);
        if ($flatConfigs === []) {
            return $this->getResolvedConfigs($userId);
        }

        $definitions = $this->definitionRepository->getConfigIdMapByKeys(array_keys($flatConfigs));

        if ($definitions->isNotEmpty()) {
            $configIdValueMap = [];
            foreach ($definitions as $configKey => $configId) {
                $configIdValueMap[$configId] = $flatConfigs[$configKey];
            }

            $this->valueRepository->upsertForUserByConfigIds($userId, $configIdValueMap);
        }

        return $this->getResolvedConfigs($userId);
    }

    protected function normalizeConfigPayload(array $configs): array
    {
        $normalized = [];

        foreach ($configs as $key => $value) {
            if (is_array($value) && array_key_exists('key', $value)) {
                $payloadKey = is_string($value['key']) ? trim($value['key']) : '';
                if ($payloadKey === '' || !array_key_exists('value', $value)) {
                    continue;
                }

                $normalized[$payloadKey] = $value['value'];
                continue;
            }

            if (!is_string($key)) {
                continue;
            }

            $payloadKey = trim($key);
            if ($payloadKey === '') {
                continue;
            }

            $normalized[$payloadKey] = $value;
        }

        return $normalized;
    }
}
