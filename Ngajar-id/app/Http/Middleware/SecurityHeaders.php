<?php

namespace App\Http  \Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * ✅ PHASE 1 SECURITY: Add critical security headers
     *
     * Protects against:
     * - MIME type sniffing
     * - Clickjacking
     * - XSS attacks
     * - Insecure transport
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Prevent MIME type sniffing - force send as-is
        $response->header('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking - don't allow iframing
        $response->header('X-Frame-Options', 'DENY');

        // XSS Protection - enable browser XSS filter
        $response->header('X-XSS-Protection', '1; mode=block');

        // Strict Transport Security - force HTTPS for future visits
        $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // Content Security Policy - restrict content sources
        $response->header('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline';");

        // Referrer Policy - limit referrer information
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy - disable dangerous browser features
        $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=()');

        return $response;
    }
}
