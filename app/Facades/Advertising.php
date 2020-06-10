<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static getProfiles(int $city_id = null)
 * @method static getProfilesAdvertisingByCity(int $city_id)
 * @method static setProfiles(int $city_id = null, bool $pay = false, $notificationNeed = true)
 * @method static clearCache(int $city_id = null)
 */
class Advertising extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'advertising';
    }
}
