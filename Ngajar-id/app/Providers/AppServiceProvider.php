<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register service aplikasi
     */
    public function register(): void
    {
        //
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
    }
}
