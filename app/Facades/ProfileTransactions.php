<?php


namespace App\Facades;


use App\Profile;
use Illuminate\Support\Facades\Facade;

/**
 * @method static setPaid(Profile $profile, float $cost)
 * @method static setVerification(Profile $profile, float $verification_cost)
 * @method static updateVerification()
 */
class ProfileTransactions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'profile_transactions';
    }
}