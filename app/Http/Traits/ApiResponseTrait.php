<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait để tái sử dụng response formatting
 */
trait ApiResponseTrait
{
    /**
     * Return success response
     */
    protected function successResponse($message, $data = null, $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Return error response
     */
    protected function errorResponse($message, $statusCode = 500, $data = null): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
}
