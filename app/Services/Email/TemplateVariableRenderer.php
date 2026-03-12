<?php

namespace App\Services\Email;

class TemplateVariableRenderer
{
    public function render(?string $content, array $variables = [], array $fallbacks = []): ?string
    {
        if ($content === null || $content === '') {
            return $content;
        }

        $merged = array_merge($fallbacks, $variables);

        return (string) preg_replace_callback('/\{\{\s*([a-zA-Z0-9_.-]+)\s*\}\}/', function ($matches) use ($merged) {
            $key = (string) ($matches[1] ?? '');
            $value = $merged[$key] ?? null;

            if ($value === null) {
                return '';
            }

            if (is_scalar($value)) {
                return (string) $value;
            }

            return '';
        }, $content);
    }
}
