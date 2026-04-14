<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests;

class ThrottleApiRequests extends ThrottleRequests
{
    /**
     * Get the throttle key for the request.
     * Uses user ID if authenticated, otherwise IP address
     */
    protected function resolveRequestSignature($request)
    {
        // Use user ID if authenticated
        if ($request->user()) {
            return $request->user()->getKey();
        }

        // Otherwise use IP address
        return $request->ip();
    }
}
