<?php


namespace App\Facades;


use App\User;
use Illuminate\Support\Facades\Facade;

/**
 * @method static rise()
 * @method static buyBanner(\Illuminate\Http\Request $request, User $user = null)
 * @method static setBanners(int $city_id = null, bool $pay = false)
 * @method static getBanners()
 * @method static getUnAvailableDays($location, int $advertisingBannerId)
 * @method static getLastUnavailableDay($location)
 */
class SalonAdvertising extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'salon_advertising';
    }
}
