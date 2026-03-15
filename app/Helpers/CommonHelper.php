<?php

if (!function_exists('buildLogContext')) {
    function buildLogContext($context = [])
    {
        try {
            $user = auth()->user();
        } catch (\Exception $e) {
            $user = null;
        }

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $defaultContext = [
            'user_id' => $user->id ?? null,
            'user_email' => $user->email ?? null,
            'ip' => request()->ip() ?? null,
            'url' => request()->fullUrl() ?? null,
            'method' => request()->method() ?? null,
            'file' => $trace[1]['file'] ?? null,
            'line' => $trace[1]['line'] ?? null,
            'request_data' => request()->all() ?? [],
        ];

        return array_merge($defaultContext, $context);
    }
}

if (!function_exists('addInfoLog')) {
    function addInfoLog($message, $context = [])
    {
        \Log::info($message, buildLogContext($context));
    }
}

if (!function_exists('addErrorLog')) {
    function addErrorLog($message, $context = [])
    {
        \Log::error($message, buildLogContext($context));
    }
}

if (!function_exists('addDebugLog')) {
    function addDebugLog($message, $context = [])
    {
        \Log::debug($message, buildLogContext($context));
    }
}

if (!function_exists('addWarningLog')) {
    function addWarningLog($message, $context = [])
    {
        \Log::warning($message, buildLogContext($context));
    }
}

if (!function_exists('debug')) {
    function debug($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        exit();
    }
}
