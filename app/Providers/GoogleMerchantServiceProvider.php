<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\GoogleMerchantApiClientInterface;
use App\Services\GoogleMerchantApiClient;

class GoogleMerchantServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(GoogleMerchantApiClientInterface::class, function ($app) {
            return new GoogleMerchantApiClient(
                config('google_merchant.rate_limit_per_minute', 60),
            );
        });
    }
}