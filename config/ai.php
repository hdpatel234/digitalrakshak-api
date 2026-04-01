<?php

return [
    'drivers' => [
        'gemini' => App\Services\Ai\Drivers\GeminiDriver::class,
        'google_gemini' => App\Services\Ai\Drivers\GeminiDriver::class,
        'openai_compatible' => App\Services\Ai\Drivers\OpenAiCompatibleDriver::class,
        'openai' => App\Services\Ai\Drivers\OpenAiCompatibleDriver::class,
    ],
];
