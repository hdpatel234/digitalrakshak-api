<?php

namespace App\Support;

use Sentry\State\Scope;

class SentryContext
{
    /**
     * Add custom context to the current Sentry scope.
     *
     * @param array $data Context data to add (e.g., ['order_id' => 123])
     * @return void
     */
    public static function set(array $data): void
    {
        if (app()->bound('sentry')) {
            \Sentry\configureScope(function (Scope $scope) use ($data) {
                foreach ($data as $key => $value) {
                    $scope->setContext('custom_' . $key, [
                        'value' => $value
                    ]);
                    $scope->setTag($key, (string) $value);
                }
            });
        }
    }
}
