<?php

namespace App\Services\Ai;

use App\Models\AiApiConfig;
use App\Models\AiModel;
use App\Models\AiPrompt;
use App\Models\AiProvider;
use Illuminate\Support\Arr;

class AiManager
{
    public function __construct(protected AiDriverFactory $factory)
    {
    }

    /**
     * @return array{success: bool, data?: array, raw_text?: string, error?: string, meta?: array}
     */
    public function generateFromPrompt(string $promptCode, array $variables = [], array $options = []): array
    {
        $prompt = $this->resolvePrompt($promptCode);
        if (!$prompt) {
            return ['success' => false, 'error' => 'AI prompt is not configured.'];
        }

        $runtime = $this->resolveRuntime($prompt);
        if (!$runtime['success']) {
            return $runtime;
        }

        /** @var AiProvider $provider */
        $provider = $runtime['provider'];
        /** @var AiApiConfig $config */
        $config = $runtime['config'];
        /** @var AiModel|null $model */
        $model = $runtime['model'] ?? null;

        $systemPrompt = $this->renderTemplate((string) ($prompt->system_prompt ?? ''), $variables);
        $userPrompt = $this->renderTemplate((string) ($prompt->user_prompt_template ?? ''), $variables);

        if ($userPrompt === '') {
            $userPrompt = $this->renderFallbackPrompt($variables);
        }

        $settings = $this->resolveSettings($prompt, $config, $options);

        // Override api_key securely if provided by the rotating accounts manager
        if (isset($options['api_key']) && is_string($options['api_key']) && $options['api_key'] !== '') {
            $config->api_key = $options['api_key'];
        }

        $driver = $this->factory->driver($provider, $config, $model);
        $response = $driver->generate($systemPrompt, $userPrompt, $settings);

        if (!$response['success']) {
            return ['success' => false, 'error' => $response['error'] ?? 'AI request failed.'];
        }

        $text = (string) ($response['text'] ?? '');
        $parsed = $this->parseResponse($text, (bool) ($prompt->parse_response ?? false));

        return [
            'success' => true,
            'data' => $parsed,
            'raw_text' => $text,
            'meta' => [
                'provider_code' => (string) ($provider->provider_code ?? ''),
                'provider_type' => (string) ($provider->provider_type ?? ''),
                'model_code' => (string) ($model?->model_code ?? $config->default_model ?? ''),
                'prompt_code' => (string) ($prompt->prompt_code ?? ''),
                'usage' => $response['usage'] ?? [],
            ],
        ];
    }

    protected function resolvePrompt(string $promptCode): ?AiPrompt
    {
        $promptCode = trim($promptCode);
        if ($promptCode === '') {
            return null;
        }

        return AiPrompt::query()
            ->where('prompt_code', $promptCode)
            ->whereIn('is_active', [true, 1, '1', 'active'])
            ->orderByDesc('version')
            ->first();
    }

    /**
     * @return array{success: bool, provider?: AiProvider, config?: AiApiConfig, model?: AiModel, error?: string}
     */
    protected function resolveRuntime(AiPrompt $prompt): array
    {
        $providerId = (int) ($prompt->provider_id ?? 0);
        $modelId = (int) ($prompt->model_id ?? 0);

        $configQuery = AiApiConfig::query()
            ->whereIn('is_active', [true, 1, '1', 'active']);

        if ($providerId > 0) {
            $configQuery->where('provider_id', $providerId);
        }

        if ($modelId > 0) {
            $configQuery->where('model_id', $modelId);
        }

        $env = 'production';
        $configQuery->where(function ($query) use ($env) {
            $query->whereNull('environment')
                ->orWhere('environment', $env);
        });

        $config = $configQuery
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->first();

        if (!$config) {
            return ['success' => false, 'error' => 'AI configuration is not configured.'];
        }

        if ($providerId <= 0) {
            $providerId = (int) ($config->provider_id ?? 0);
        }

        if ($modelId <= 0) {
            $modelId = (int) ($config->model_id ?? 0);
        }

        $provider = $providerId > 0 ? AiProvider::query()->find($providerId) : null;
        if (!$provider) {
            return ['success' => false, 'error' => 'AI provider is not configured.'];
        }

        $model = $modelId > 0 ? AiModel::query()->find($modelId) : null;

        return [
            'success' => true,
            'provider' => $provider,
            'config' => $config,
            'model' => $model,
        ];
    }

    protected function resolveSettings(AiPrompt $prompt, AiApiConfig $config, array $options): array
    {
        $responseFormat = $prompt->response_format ?? null;
        $responseFormat = is_string($responseFormat) && $responseFormat !== ''
            ? $this->normalizeResponseFormat($responseFormat, $prompt)
            : null;

        $settings = [
            'temperature' => $prompt->temperature ?? $config->default_temperature ?? 0.1,
            'max_tokens' => $prompt->max_tokens ?? $config->default_max_tokens ?? null,
            'top_p' => $config->default_top_p ?? null,
            'frequency_penalty' => $config->default_frequency_penalty ?? null,
            'presence_penalty' => $config->default_presence_penalty ?? null,
            'response_format' => $responseFormat,
            'timeout' => $options['timeout'] ?? 60,
        ];

        return Arr::where($settings, static fn($value) => $value !== null && $value !== '');
    }

    protected function normalizeResponseFormat(string $format, AiPrompt $prompt): mixed
    {
        $format = trim($format);
        if ($format === '') {
            return null;
        }

        if ($format === 'json_object') {
            return ['type' => 'json_object'];
        }

        if ($format === 'json_schema') {
            $schema = $prompt->response_schema ?? null;
            if (is_string($schema) && $schema !== '') {
                $schema = json_decode($schema, true);
            }

            if (is_array($schema)) {
                return ['type' => 'json_schema', 'json_schema' => $schema];
            }
        }

        if (str_starts_with($format, '{')) {
            $decoded = json_decode($format, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    protected function parseResponse(string $text, bool $parseResponse): array
    {
        if (!$parseResponse) {
            return ['raw' => $text];
        }

        $jsonText = preg_replace('/^```json\s*|\s*```$/', '', $text);
        $jsonText = trim((string) $jsonText);

        $decoded = json_decode($jsonText, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $jsonText, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return ['raw' => $text];
    }

    protected function renderTemplate(string $template, array $variables): string
    {
        if ($template === '') {
            return '';
        }

        foreach ($variables as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $template = str_replace($placeholder, (string) $value, $template);
            $template = str_replace('{{ ' . $key . ' }}', (string) $value, $template);
        }

        return $template;
    }

    protected function renderFallbackPrompt(array $variables): string
    {
        $filename = (string) ($variables['filename'] ?? 'resume');
        $text = (string) ($variables['resume_text'] ?? '');

        return "Extract resume data from the following file and return JSON only.\n\nFILENAME: {$filename}\n\nRESUME TEXT:\n{$text}";
    }
}

