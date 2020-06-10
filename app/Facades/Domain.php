<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static getSubDomainPrefix()
 * @method static subDomainUrl(string $prefix, string $path = null)
 * @method static getDomainCityByIp()
 * @method static checkCityId(int $city_id)
 * @method static getDomainCity()
 * @method static getCurrentCityId()
 * @method static getDefaultCity()
 * @method static getDefaultCountry()
 */
class Domain extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'domain';
    }
}