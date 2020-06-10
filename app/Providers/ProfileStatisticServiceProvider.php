<?php

namespace App\Providers;

use App\Services\ProfileStatistic;
use Illuminate\Support\ServiceProvider;

class ProfileStatisticServiceProvider extends ServiceProvider
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
        $this->app->singleton('profile_statistic', function () {
            return new ProfileStatistic;
        });
    }
}
