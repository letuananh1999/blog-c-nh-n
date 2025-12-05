<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ApiResponseService
{
  /**
   * Return success response
   */
  public static function success(string $message, mixed $data = null, int $statusCode = 200): JsonResponse
  {
    $response = [
      'status' => true,
      'message' => $message,
    ];

    if ($data !== null) {
      $response['data'] = $data;
    }

    return response()->json($response, $statusCode);
  }

  /**
   * Return error response
   */
  public static function error(string $message, mixed $data = null, int $statusCode = 500): JsonResponse
  {
    $response = [
      'status' => false,
      'message' => $message,
    ];

    if ($data !== null) {
      $response['data'] = $data;
    }

    return response()->json($response, $statusCode);
  }

  /**
   * Return unauthorized response (403)
   */
  public static function unauthorized(string $message = 'Bạn không có quyền thực hiện hành động này!'): JsonResponse
  {
    return self::error($message, null, 403);
  }

  /**
   * Return not found response (404)
   */
  public static function notFound(string $message = 'Tài nguyên không tìm thấy!'): JsonResponse
  {
    return self::error($message, null, 404);
  }

  /**
   * Return validation error response (422)
   */
  public static function validationError(array $errors): JsonResponse
  {
    return response()->json([
      'status' => false,
      'message' => 'Validation failed',
      'errors' => $errors,
    ], 422);
  }

  /**
   * Return server error response (500)
   */
  public static function serverError(string $message = 'Có lỗi xảy ra trên server!'): JsonResponse
  {
    return self::error($message, null, 500);
  }
}
