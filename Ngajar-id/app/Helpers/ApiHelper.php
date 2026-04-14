<?php

if (!function_exists('apiResponse')) {
    /**
     * Return a successful API response
     */
    function apiResponse($data = null, string $message = 'Success', int $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}

if (!function_exists('apiError')) {
    /**
     * Return an error API response
     */
    function apiError(string $message = 'Error', array $errors = [], int $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}

if (!function_exists('apiNotFound')) {
    /**
     * Return a not found response
     */
    function apiNotFound(string $message = 'Resource not found')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 404);
    }
}

if (!function_exists('apiUnauthorized')) {
    /**
     * Return an unauthorized response
     */
    function apiUnauthorized(string $message = 'Unauthorized')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 401);
    }
}

if (!function_exists('apiForbidden')) {
    /**
     * Return a forbidden response
     */
    function apiForbidden(string $message = 'Forbidden')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }
}

if (!function_exists('apiValidationError')) {
    /**
     * Return validation error response
     */
    function apiValidationError(array $errors, string $message = 'Validation failed')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }
}

if (!function_exists('apiServerError')) {
    /**
     * Return server error response
     */
    function apiServerError(string $message = 'Server error')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 500);
    }
}

if (!function_exists('apiPaginatedResponse')) {
    /**
     * Return paginated response
     */
    function apiPaginatedResponse($data, string $message = 'Success', int $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
                'total' => $data->total(),
            ],
        ], $statusCode);
    }
}
