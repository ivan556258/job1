<?php


namespace App\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * @method static applyFilters(\Illuminate\Http\Request $request, $profiles)
 * @method static getFiltersVariables(\Illuminate\Http\Request $request): array
 */
class ProfileFilters extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'profile_filters';
    }
}