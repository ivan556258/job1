<?php


namespace App\Facades;


use App\Salon;
use Illuminate\Support\Facades\Facade;

/**
 * @method static createExtraData(\Illuminate\Http\Request $request, Salon $salon)
 * @method static everyMonthActivation()
 */
class SalonService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'salon_service';
    }
}