<?php

namespace App\Providers;

use App\Services\ProfileService;
use Illuminate\Support\ServiceProvider;

class ProfileServiceServiceProvider extends ServiceProvider
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
        $this->app->singleton('profile_service', function() {
            return new ProfileService();
        });
    }
}
