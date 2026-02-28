<?php

namespace App\Http\Middleware;

use App\Enums\UserConfigKey;
use App\Models\UserConfigDefinition;
use App\Models\UserConfigValue;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetUserPreferredLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale($this->resolveLocale());

        return $next($request);
    }

    protected function resolveLocale(): string
    {
        $appFallbackLocale = (string) config('app.locale', 'en');

        $languageDefinition = UserConfigDefinition::query()
            ->where(UserConfigDefinition::CONFIG_KEY, UserConfigKey::LANGUAGE->value)
            ->first([
                UserConfigDefinition::ID,
                UserConfigDefinition::DEFAULT_VALUE,
                UserConfigDefinition::POSSIBLE_VALUES,
            ]);

        if (!$languageDefinition) {
            return $this->ensureSupportedLocale($appFallbackLocale, $appFallbackLocale);
        }

        $possibleValues = $languageDefinition->{UserConfigDefinition::POSSIBLE_VALUES};
        $allowedLocales = is_array($possibleValues) ? array_keys($possibleValues) : [];

        $defaultLocale = (string) ($languageDefinition->{UserConfigDefinition::DEFAULT_VALUE} ?: $appFallbackLocale);
        $defaultLocale = $this->ensureSupportedLocale($defaultLocale, $appFallbackLocale, $allowedLocales);

        $user = Auth::guard('api')->user() ?? Auth::user();
        if (!$user) {
            return $defaultLocale;
        }

        $userLocale = UserConfigValue::query()
            ->where(UserConfigValue::USER_ID, $user->getAuthIdentifier())
            ->where(UserConfigValue::CONFIG_ID, $languageDefinition->{UserConfigDefinition::ID})
            ->value(UserConfigValue::VALUE);

        if (!is_string($userLocale) || trim($userLocale) === '') {
            return $defaultLocale;
        }

        return $this->ensureSupportedLocale(trim($userLocale), $defaultLocale, $allowedLocales);
    }

    protected function ensureSupportedLocale(string $locale, string $fallbackLocale, array $allowedLocales = []): string
    {
        if (trim($locale) === '') {
            return $fallbackLocale;
        }

        if ($allowedLocales !== [] && !in_array($locale, $allowedLocales, true)) {
            return $fallbackLocale;
        }

        if (!is_dir(lang_path($locale))) {
            return $fallbackLocale;
        }

        return $locale;
    }
}
