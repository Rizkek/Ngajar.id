<?php

namespace App\Exceptions;

use Exception;

class ResourceNotFoundException extends Exception
{
    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => 'Resource not found',
            'error' => $this->message,
        ], 404);
    }
}

class UnauthorizedException extends Exception
{
    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized access',
            'error' => $this->message,
        ], 403);
    }
}

class ValidationFailedException extends Exception
{
    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'error' => $this->message,
        ], 422);
    }
}

class ConflictException extends Exception
{
    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => 'Resource conflict',
            'error' => $this->message,
        ], 409);
    }
}
