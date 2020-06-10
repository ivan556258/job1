<?php

namespace App\Console;

use App\Events\ProfileAdvertisingWasUpdated;
use App\Facades\Advertising;
use App\Facades\NotificationServiceFacade;
use App\Facades\ProfileStatistic;
use App\Facades\ProfileTransactions;
use App\Facades\SalonAdvertising;
use App\Facades\SalonService;
use App\UpdateSiteMap;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            ProfileStatistic::insertToDB();
        })->hourly();

        $schedule->call(function () {
            Advertising::setProfiles(null, true, false);
        })->hourly();

        $schedule->call(function () {
            SalonAdvertising::setBanners(null, true);
        })->dailyAt('05:00');

        $schedule->call(function () {
            ProfileTransactions::updateVerification();
        })->dailyAt('05:00');

        $schedule->command('schedule:send-email')->daily();

        $schedule->call(function () {
            SalonService::everyMonthActivation();
        })->dailyAt('05:00');

        $schedule->call(function () {
            NotificationServiceFacade::sendNotificationAboutPositionDecrease();
        })->dailyAt('14:10');

        $schedule->call(function () {
            $updateSiteMaps = UpdateSiteMap::all();
            foreach ($updateSiteMaps as $updateSiteMap) {
                $parameters = [];
                if ($updateSiteMap->city_id) {
                    $parameters = [
                        '--city_id' => $updateSiteMap->city_id
                    ];
                }
                Artisan::call('generate:sitemap', $parameters);
            }
        })->dailyAt('05:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
