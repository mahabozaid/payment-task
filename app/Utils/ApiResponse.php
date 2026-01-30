<?php

namespace App\Utils;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\AbstractPaginator;

class ApiResponse
{
    public static function success(
        string $message = 'success',
        mixed $data = [],
        int $httpStatus = 200,
        int $code = 200,
        array $meta = []
    ): JsonResponse {
        // If data is a paginator, extract pagination info
        if ($data instanceof AbstractPaginator || data_get($data, 'resource') instanceof LengthAwarePaginator) {
            $meta = array_merge($meta, [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]);

            $data = $data->items();
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'code' => $code,
            'data' => $data,
            'meta' => $meta,
        ], $httpStatus);
    }
    public static function fail(
        string $message = 'error',
        int $code = 400,
        int $httpStatus = 400,
        mixed $errors = [],
        array $meta = []
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'code' => $code,
            'errors' => $errors,
            'meta' => $meta,
        ], $httpStatus);
    }
}
