<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTokenExpiration
{
    /**
     * Check if user's API token has expired
     * If expired, revoke it and return 401
     *
     * ✅ PHASE 1 SECURITY: Enforce token expiration
     */
    public function handle(Request $request, Closure $next)
    {
        // Only check for API requests with Sanctum auth
        if ($request->user() && auth('sanctum')->check()) {
            $token = $request->user()->currentAccessToken();

            // Token exists and has expiration set
            if ($token && $token->expires_at && $token->expires_at->isPast()) {
                // Token has expired - revoke it
                $token->delete();

                return response()->json([
                    'success' => false,
                    'message' => 'Your session has expired. Please login again.',
                    'error_code' => 'TOKEN_EXPIRED',
                ], 401);
            }

            // Update last_used_at timestamp if token exists
            if ($token) {
                $token->update(['last_used_at' => now()]);
            }
        }

        return $next($request);
    }
}
