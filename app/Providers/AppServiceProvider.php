<?php

namespace App\Providers;

use App\Interfaces\GoogleMerchantApiClientInterface;
use App\Models\Feed;
use App\Policies\FeedPolicy;
use App\Services\GoogleMerchantApiClient;
use Gate;
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
        Gate::policy(Feed::class, FeedPolicy::class);

    }
}
