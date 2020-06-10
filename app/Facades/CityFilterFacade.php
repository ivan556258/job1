<?php


namespace App\Facades;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * @method static applyFilters(Request $request, $cities)
 * @method static getFiltersVariables(Request $request)
 */
class CityFilterFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'city_filter';
    }
}