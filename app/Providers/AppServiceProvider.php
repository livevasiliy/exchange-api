<?php

namespace App\Providers;

use App\Contracts\ExchangeServiceContract;
use App\Services\BlockchainInfoService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ExchangeServiceContract::class, BlockchainInfoService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
