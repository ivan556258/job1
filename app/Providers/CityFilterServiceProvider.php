<?php

namespace App\Providers;

use App\Services\CityFilter;
use Illuminate\Support\ServiceProvider;

class CityFilterServiceProvider extends ServiceProvider
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
        $this->app->singleton('city_filter', function () {
            return new CityFilter();
        });
    }
}
