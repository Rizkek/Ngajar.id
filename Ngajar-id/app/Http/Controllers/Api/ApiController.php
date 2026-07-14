<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;


class ApiController extends Controller
{
    use AuthorizesRequests, ValidatesRequests, ApiResponse;

    /**
     * Base API Controller dengan built-in API response methods
     */
}
