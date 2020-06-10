<?php


namespace App\Facades;


use App\Salon;
use App\User;
use Illuminate\Support\Facades\Facade;

/**
 * @method static setActive(Salon $salon = null): bool
 * @method static createTransaction(float $amount, int $profile_id = null, string $comment = '', User $user = null)
 */
class SalonTransactions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'salon_transactions';
    }
}