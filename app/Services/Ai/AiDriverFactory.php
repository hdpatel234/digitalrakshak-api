<?php

namespace App\Services\Ai;

use App\Models\AiApiConfig;
use App\Models\AiModel;
use App\Models\AiProvider;
use App\Services\Ai\Drivers\AbstractAiDriver;
use App\Services\Ai\Drivers\GeminiDriver;
use App\Services\Ai\Drivers\OpenAiCompatibleDriver;
use InvalidArgumentException;

class AiDriverFactory
{
    public function driver(AiProvider $provider, AiApiConfig $config, ?AiModel $model = null): AbstractAiDriver
    {
        $providerKey = $this->normalizeKey((string) $provider->provider_name);

        $drivers = config('ai.drivers', [
            'gemini' => GeminiDriver::class,
            'openai_compatible' => OpenAiCompatibleDriver::class,
        ]);

        $driverClass = $drivers[$providerKey] ?? null;

        if (!is_string($driverClass) || !class_exists($driverClass)) {
            throw new InvalidArgumentException("Unsupported AI provider [$providerKey].");
        }

        if (!is_subclass_of($driverClass, AbstractAiDriver::class)) {
            throw new InvalidArgumentException("AI driver [$driverClass] must extend " . AbstractAiDriver::class . '.');
        }

        return new $driverClass($provider, $config, $model);
    }

    protected function normalizeKey(string $value): string
    {
        $value = strtolower(trim($value));
        if ($value === '') {
            return '';
        }

        $value = preg_replace('/[^a-z0-9]+/', '_', $value);
        return trim((string) $value, '_');
    }
}
