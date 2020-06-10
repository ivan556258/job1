<?php

namespace App\Providers;

use App\Services\SalonAdvertising;
use Illuminate\Support\ServiceProvider;

class SalonAdvertisingServiceProvider extends ServiceProvider
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
        $this->app->singleton('salon_advertising', function () {
            return new SalonAdvertising();
        });
    }
}
