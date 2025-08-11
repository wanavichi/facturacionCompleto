<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(mixed $data = null, string $message = 'OK', int $code = 200, array $meta = []): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $payload['data'] = $data;
        }

        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $code);
    }

    protected function error(string $message = 'Error', int $code = 400, mixed $errors = null, array $meta = []): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $payload['errors'] = $errors;
        }

        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $code);
    }
}
