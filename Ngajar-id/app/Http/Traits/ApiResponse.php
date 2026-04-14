<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Success response
     */
    public function success(
        $data = null,
        string $message = 'Success',
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Success with pagination - handles both object and manual parameters
     */
    public function successWithPagination(
        $data,
        string $message = 'Success',
        int $total = null,
        int $perPage = null,
        int $page = null,
        array $meta = [],
        int $statusCode = 200
    ): JsonResponse {
        // If data is a paginate object
        if ($total === null && is_object($data) && method_exists($data, 'items')) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                ],
                'meta' => $meta,
            ], $statusCode);
        }

        // If manual pagination parameters passed
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'current_page' => $page ?? 1,
                'per_page' => $perPage ?? 10,
                'total' => $total ?? 0,
                'last_page' => $total && $perPage ? ceil($total / $perPage) : 0,
            ],
            'meta' => $meta,
        ], $statusCode);
    }

    /**
     * Error response - flexible to accept statusCode or errors array
     */
    public function error(
        string $message = 'Error',
        $errorsOrStatusCode = [],
        int $statusCode = null
    ): JsonResponse {
        // If second param is integer, treat it as status code
        if (is_int($errorsOrStatusCode)) {
            $errors = [];
            $statusCode = $errorsOrStatusCode;
        } else {
            $errors = is_array($errorsOrStatusCode) ? $errorsOrStatusCode : [];
            $statusCode = $statusCode ?? 400;
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    /**
     * Not found response
     */
    public function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 404);
    }

    /**
     * Unauthorized response
     */
    public function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 401);
    }

    /**
     * Forbidden response
     */
    public function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }

    /**
     * Validation error response
     */
    public function validationError(array $errors): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors,
        ], 422);
    }

    /**
     * Server error response
     */
    public function serverError(string $message = 'Server error'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 500);
    }
}
