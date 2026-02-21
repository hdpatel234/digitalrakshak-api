<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success($message = '', $data = [], $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => __($message),
            'data' => $data,
            'timestamp' => now()->toDateTimeString(),
        ], $code);
    }

    protected function error($message = '', $code = 400, $data = []): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => __($message),
            'data' => $data,
            'timestamp' => now()->toDateTimeString(),
        ], $code);
    }

    protected function validationError($errors, $message = 'common.validation_error', $code = 422): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => __($message),
            'errors' => $errors,
            'timestamp' => now()->toDateTimeString(),
        ], $code);
    }
}