<?php

namespace App\Providers;

use App\AdvertisingBanner;
use App\City;
use App\Country;
use App\Metro;
use App\Observers\CentralBankCurrencyObserver;
use App\Observers\CityObserver;
use App\Observers\CountryObserver;
use App\Observers\MetroObserver;
use App\Observers\ProfileObserver;
use App\Observers\ProfileStatisticObserver;
use App\Observers\ReviewAnswerObserver;
use App\Observers\ReviewObserver;
use App\Observers\SalonObserver;
use App\Observers\ServiceObserver;
use App\Observers\SettingObserver;
use App\Observers\TicketMessageObserver;
use App\Observers\TicketObserver;
use App\Observers\TransactionObserver;
use App\Observers\UserAdvertisingObserver;
use App\Observers\UserObserver;
use App\Profile;
use App\ProfileStatistic;
use App\Review;
use App\ReviewAnswer;
use App\Salon;
use App\Service;
use App\Setting;
use App\Ticket;
use App\TicketMessage;
use App\Transaction;
use App\User;
use Grechanyuk\CentralBankCurrency\Models\CentralBankCurrency;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
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
        Setting::observe(SettingObserver::class);
        TicketMessage::observe(TicketMessageObserver::class);
        Transaction::observe(TransactionObserver::class);
        Metro::observe(MetroObserver::class);
        Service::observe(ServiceObserver::class);
        ReviewAnswer::observe(ReviewAnswerObserver::class);
        Country::observe(CountryObserver::class);
        Profile::observe(ProfileObserver::class);
        AdvertisingBanner::observe(UserAdvertisingObserver::class);
        ProfileStatistic::observe(ProfileStatisticObserver::class);
        City::observe(CityObserver::class);
        Salon::observe(SalonObserver::class);
        CentralBankCurrency::observe(CentralBankCurrencyObserver::class);
        User::observe(UserObserver::class);
        Review::observe(ReviewObserver::class);
        Ticket::observe(TicketObserver::class);
    }
}
