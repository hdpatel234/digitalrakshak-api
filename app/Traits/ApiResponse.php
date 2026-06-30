<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait ApiResponse
{
    protected function responseTimestamp(): string
    {
        return now()->format((string) config('app.user_datetime_format', 'Y-m-d H:i:s'));
    }

    protected function success($message = '', $data = [], $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => __($message),
            'data' => $data,
            'timestamp' => $this->responseTimestamp(),
        ], $code);
    }

    protected function error($message = '', $code = 400, $data = []): JsonResponse
    {
        $statusCode = (is_numeric($code) && (int)$code >= 100 && (int)$code <= 599) ? (int)$code : 500;

        return response()->json([
            'status' => false,
            'message' => __($message),
            'data' => $data,
            'timestamp' => $this->responseTimestamp(),
        ], $statusCode);
    }

    protected function validationError($errors, $message = 'common.validation_error', $code = 422): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => __($message),
            'errors' => $errors,
            'timestamp' => $this->responseTimestamp(),
        ], $code);
    }

    protected function downloadResponse(
        callable $callback,
        string $filename,
        array $headers = []
    ): StreamedResponse {
        return response()->streamDownload($callback, $filename, $headers);
    }
}
