<?php

namespace App\Services\Ai\Drivers;

use App\Models\AiApiConfig;
use App\Models\AiModel;
use App\Models\AiProvider;

abstract class AbstractAiDriver
{
    protected AiProvider $provider;
    protected AiApiConfig $config;
    protected ?AiModel $model;

    public function __construct(AiProvider $provider, AiApiConfig $config, ?AiModel $model = null)
    {
        $this->provider = $provider;
        $this->config = $config;
        $this->model = $model;
    }

    /**
     * @return array{success: bool, text?: string, raw?: array, error?: string, usage?: array}
     */
    abstract public function generate(string $systemPrompt, string $userPrompt, array $settings = []): array;
}
