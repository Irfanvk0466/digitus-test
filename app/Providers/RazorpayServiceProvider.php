<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\RazorpayService; 

class RazorpayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(RazorpayService::class, function ($app) {
            return new RazorpayService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
