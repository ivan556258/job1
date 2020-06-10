<?php

namespace App\Providers;

use App\Services\SalonService;
use Illuminate\Support\ServiceProvider;

class SalonServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('salon_service', function () {
            return new SalonService();
        });
    }
}
