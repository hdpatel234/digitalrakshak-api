<?php

namespace App\Services\Ai\Drivers;

use Illuminate\Support\Facades\Http;

class OpenAiCompatibleDriver extends AbstractAiDriver
{
    public function generate(string $systemPrompt, string $userPrompt, array $settings = []): array
    {
        $apiKey = trim((string) ($this->config->api_key ?? ''));
        if ($apiKey === '') {
            return ['success' => false, 'error' => 'AI API key is not configured.'];
        }

        $baseUrl = rtrim((string) ($this->config->base_url ?? 'https://api.openai.com/v1'), '/');
        $modelCode = $this->resolveModelCode();
        if ($modelCode === '') {
            return ['success' => false, 'error' => 'AI model is not configured.'];
        }

        $url = $baseUrl . '/chat/completions';

        $payload = [
            'model' => $modelCode,
            'messages' => array_values(array_filter([
                trim($systemPrompt) !== '' ? ['role' => 'system', 'content' => $systemPrompt] : null,
                ['role' => 'user', 'content' => $userPrompt],
            ])),
            'temperature' => $settings['temperature'] ?? 0.1,
            'max_tokens' => $settings['max_tokens'] ?? null,
            'top_p' => $settings['top_p'] ?? 1,
        ];

        $responseFormat = $settings['response_format'] ?? null;
        if ($responseFormat) {
            $payload['response_format'] = $responseFormat;
        }

        $timeout = (int) ($settings['timeout'] ?? 60);

        $response = Http::timeout($timeout)
            ->withHeaders(['Authorization' => 'Bearer ' . $apiKey])
            ->post($url, $payload);

        if ($response->failed()) {
            return [
                'success' => false,
                'error' => 'AI API error: HTTP ' . $response->status(),
                'raw' => $response->json(),
            ];
        }

        $data = $response->json();
        $text = data_get($data, 'choices.0.message.content');

        if (!is_string($text) || trim($text) === '') {
            return [
                'success' => false,
                'error' => 'AI response did not include text content.',
                'raw' => $data,
            ];
        }

        return [
            'success' => true,
            'text' => $text,
            'raw' => $data,
            'usage' => data_get($data, 'usage', []),
        ];
    }

    protected function resolveModelCode(): string
    {
        $modelCode = trim((string) ($this->model?->model_code ?? ''));
        if ($modelCode !== '') {
            return $modelCode;
        }

        return trim((string) ($this->config->default_model ?? ''));
    }
}
