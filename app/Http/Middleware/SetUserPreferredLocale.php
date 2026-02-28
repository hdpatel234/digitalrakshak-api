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
        $this->setDateTimeFormatConfig();

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

    protected function setDateTimeFormatConfig(): void
    {
        $appDefaultDateFormat = 'Y-m-d';
        $appDefaultTimeFormat = 'H:i:s';

        $dateFormat = $this->resolveUserConfigRawValue(UserConfigKey::DATE_FORMAT, 'YYYY-MM-DD');
        $timeFormat = $this->resolveUserConfigRawValue(UserConfigKey::TIME_FORMAT, '24');

        $phpDateFormat = $this->toPhpDateFormat($dateFormat);
        if (!$this->isValidPhpDateFormat($phpDateFormat)) {
            $phpDateFormat = $appDefaultDateFormat;
        }

        $phpTimeFormat = $this->toPhpTimeFormat($timeFormat);
        if (!$this->isValidPhpDateFormat($phpTimeFormat)) {
            $phpTimeFormat = $appDefaultTimeFormat;
        }

        $dateTimeFormat = trim($phpDateFormat . ' ' . $phpTimeFormat);

        config([
            'app.user_date_format' => $phpDateFormat,
            'app.user_time_format' => $phpTimeFormat,
            'app.user_datetime_format' => $dateTimeFormat,
        ]);
    }

    protected function resolveUserConfigRawValue(UserConfigKey $configKey, string $fallback): string
    {
        $definition = UserConfigDefinition::query()
            ->where(UserConfigDefinition::CONFIG_KEY, $configKey->value)
            ->first([
                UserConfigDefinition::ID,
                UserConfigDefinition::DEFAULT_VALUE,
                UserConfigDefinition::POSSIBLE_VALUES,
            ]);

        if (!$definition) {
            return $fallback;
        }

        $possibleValues = $definition->{UserConfigDefinition::POSSIBLE_VALUES};
        $allowedValues = is_array($possibleValues) ? array_keys($possibleValues) : [];

        $resolved = (string) ($definition->{UserConfigDefinition::DEFAULT_VALUE} ?: $fallback);
        if (!$this->isAllowedPossibleValue($resolved, $allowedValues)) {
            $resolved = $fallback;
        }

        $user = Auth::guard('api')->user() ?? Auth::user();
        if (!$user) {
            return $resolved;
        }

        $userValue = UserConfigValue::query()
            ->where(UserConfigValue::USER_ID, $user->getAuthIdentifier())
            ->where(UserConfigValue::CONFIG_ID, $definition->{UserConfigDefinition::ID})
            ->value(UserConfigValue::VALUE);

        if (!is_string($userValue) || trim($userValue) === '') {
            return $resolved;
        }

        $userValue = trim($userValue);
        if (!$this->isAllowedPossibleValue($userValue, $allowedValues)) {
            return $resolved;
        }

        return $userValue;
    }

    protected function isAllowedPossibleValue(string $value, array $allowedValues): bool
    {
        if ($value === '') {
            return false;
        }

        if ($allowedValues !== [] && !in_array($value, $allowedValues, true)) {
            return false;
        }

        return true;
    }

    protected function toPhpDateFormat(string $format): string
    {
        return $this->convertRawFormatToPhpFormat($format, [
            'YYYY' => 'Y',
            'YY' => 'y',
            'MM' => 'm',
            'DD' => 'd',
        ]);
    }

    protected function toPhpTimeFormat(string $format): string
    {
        if ($format === '12') {
            return 'h:i A';
        }

        if ($format === '24') {
            return 'H:i';
        }

        return $this->convertRawFormatToPhpFormat($format, [
            'HH' => 'H',
            'hh' => 'h',
            'mm' => 'i',
            'ss' => 's',
            'A' => 'A',
            'a' => 'a',
        ]);
    }

    protected function convertRawFormatToPhpFormat(string $format, array $tokenMap): string
    {
        $format = trim($format);
        if ($format === '') {
            return '';
        }

        if (!preg_match('/^[A-Za-z0-9\\s:\\/\\-._]+$/', $format)) {
            return '';
        }

        $search = array_keys($tokenMap);
        usort($search, static fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        $phpFormat = '';
        $cursor = 0;
        $length = strlen($format);

        while ($cursor < $length) {
            $matched = false;

            foreach ($search as $token) {
                if (substr($format, $cursor, strlen($token)) !== $token) {
                    continue;
                }

                $phpFormat .= $tokenMap[$token];
                $cursor += strlen($token);
                $matched = true;
                break;
            }

            if ($matched) {
                continue;
            }

            $char = $format[$cursor];
            if (preg_match('/[\\s:\\/\\-._]/', $char)) {
                $phpFormat .= $char;
                $cursor++;
                continue;
            }

            return '';
        }

        return $phpFormat;
    }

    protected function isValidPhpDateFormat(string $format): bool
    {
        if (trim($format) === '') {
            return false;
        }

        try {
            now()->format($format);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
