<?php

namespace App\Providers;

use App\Services\ProfileTransactions;
use Illuminate\Support\ServiceProvider;

class ProfileTransactionsServiceProvider extends ServiceProvider
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
        $this->app->singleton('profile_transactions', function () {
            return new ProfileTransactions();
        });
    }
}
