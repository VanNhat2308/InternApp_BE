<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
          Broadcast::routes([
        'middleware' => ['auth:api_admin,api_sinhvien'], // cho cả 2 loại guard
        'prefix' => 'api' // để endpoint thành /api/broadcasting/auth
    ]);

    require base_path('routes/channels.php');
    }
}
