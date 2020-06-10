<?php

namespace App\Providers;

use App\Services\SalonTransactions;
use Illuminate\Support\ServiceProvider;

class SalonTransactionServiceProvider extends ServiceProvider
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
        $this->app->singleton('salon_transactions', function () {
            return new SalonTransactions();
        });
    }
}
