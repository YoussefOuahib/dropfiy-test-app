<?php

namespace App\Providers;

use App\Interfaces\GoogleMerchantApiClientInterface;
use App\Services\GoogleMerchantApiClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GoogleMerchantApiClientInterface::class, GoogleMerchantApiClient::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
