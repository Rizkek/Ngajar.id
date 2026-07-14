<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register service aplikasi
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\KelasRepositoryInterface::class,
            \App\Repositories\Eloquent\KelasRepository::class
        );
        
        $this->app->bind(
            \App\Repositories\Contracts\MateriRepositoryInterface::class,
            \App\Repositories\Eloquent\MateriRepository::class
        );
    }

    /**
     * Bootstrap service aplikasi
     */
    public function boot(): void
    {
        // Enforce Strict Mode di Local untuk mencegah N+1 Query (Gratis & Efektif)
        \Illuminate\Database\Eloquent\Model::preventLazyLoading(!app()->isProduction());

        // Gunakan HTTPS di Production (Wajib buat Supabase/Deployment modern)
        if (app()->isProduction()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // ✅ PHASE 1 SECURITY: Configure Rate Limiting
        $this->configureRateLimiting();
    }

    /**
     * Configure rate limiting for various endpoints
     */
    protected function configureRateLimiting(): void
    {
        // ✅ LOGIN: 5 attempts per minute per email/IP
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->email ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many login attempts. Try again in 1 minute.',
                        'retry_after' => 60,
                    ], 429);
                });
        });

        // ✅ PASSWORD RESET: 3 attempts per 15 minutes per email
        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perMinutes(15, 3)
                ->by($request->email ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many password reset attempts. Try again in 15 minutes.',
                        'retry_after' => 900,
                    ], 429);
                });
        });

        // ✅ API: 100 requests per minute per user/IP
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(100)
                ->by(optional($request->user())->id ?: $request->ip());
        });

        // ✅ PUBLIC API: 30 requests per minute (unauthenticated)
        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Rate limit exceeded. Maximum 30 requests per minute.',
                        'retry_after' => 60,
                    ], 429);
                });
        });

        // ✅ REGISTRATION: 5 per hour per IP (prevent bot spam)
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(5)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many registration attempts. Try again later.',
                    ], 429);
                });
        });

        // ✅ WEBHOOK: 1000 per hour (payment webhooks from Midtrans/Xendit)
        RateLimiter::for('webhook', function (Request $request) {
            return Limit::perHour(1000)
                ->by($request->ip());
        });
    }
}
