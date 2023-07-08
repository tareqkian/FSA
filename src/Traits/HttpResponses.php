<?php

namespace Tarek\Fsa\Traits;

use Illuminate\Http\JsonResponse;

trait HttpResponses {
    protected function success(array $data, null|string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            "status" => "Request was successful.",
            "message" => $message,
            "data" => $data
        ], $code);
    }

    protected function error(array $data, null|string $message = null, int $code): JsonResponse
    {
        return response()->json([
            "status" => "Error has occurred",
            "message" => $message,
            "data" => $data
        ], $code);
    }
}
