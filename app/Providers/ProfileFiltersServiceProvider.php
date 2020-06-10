<?php

namespace App\Providers;

use App\Services\ProfileFilters;
use Illuminate\Support\ServiceProvider;

class ProfileFiltersServiceProvider extends ServiceProvider
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
        $this->app->singleton('profile_filters', function () {
            return new ProfileFilters();
        });
    }
}
