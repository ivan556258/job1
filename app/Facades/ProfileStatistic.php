<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static insertToDB()
 * @method static clear()
 */
class ProfileStatistic extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'profile_statistic';
    }
}