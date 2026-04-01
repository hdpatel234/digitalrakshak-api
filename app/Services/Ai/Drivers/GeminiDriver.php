<?php

namespace App\Services\Ai\Drivers;

use Illuminate\Support\Facades\Http;

class GeminiDriver extends AbstractAiDriver
{
    public function generate(string $systemPrompt, string $userPrompt, array $settings = []): array
    {
        $apiKey = trim((string) ($this->config->api_key ?? ''));
        if ($apiKey === '') {
            return ['success' => false, 'error' => 'Gemini API key is not configured.'];
        }

        $baseUrl = rtrim((string) ($this->config->base_url ?? 'https://generativelanguage.googleapis.com'), '/');
        $modelCode = $this->resolveModelCode();
        if ($modelCode === '') {
            return ['success' => false, 'error' => 'Gemini model is not configured.'];
        }

        $modelPath = str_starts_with($modelCode, 'models/') ? $modelCode : 'models/' . $modelCode;
        $url = $baseUrl . '/v1beta/' . $modelPath . ':generateContent';

        $promptText = trim($systemPrompt) !== ''
            ? trim($systemPrompt) . "\n\n" . trim($userPrompt)
            : trim($userPrompt);

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $promptText],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => $settings['temperature'] ?? 0.1,
                'maxOutputTokens' => $settings['max_tokens'] ?? 4096,
                'topP' => $settings['top_p'] ?? 0.95,
                'topK' => $settings['top_k'] ?? 40,
            ],
        ];

        $timeout = (int) ($settings['timeout'] ?? 60);

        $response = Http::timeout($timeout)
            ->withQueryParameters(['key' => $apiKey])
            ->post($url, $payload);

        if ($response->failed()) {
            return [
                'success' => false,
                'error' => 'Gemini API error: HTTP ' . $response->status(),
                'raw' => $response->json(),
            ];
        }

        $data = $response->json();
        $text = data_get($data, 'candidates.0.content.parts.0.text');

        if (!is_string($text) || trim($text) === '') {
            return [
                'success' => false,
                'error' => 'Gemini response did not include text content.',
                'raw' => $data,
            ];
        }

        return [
            'success' => true,
            'text' => $text,
            'raw' => $data,
            'usage' => data_get($data, 'usageMetadata', []),
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
