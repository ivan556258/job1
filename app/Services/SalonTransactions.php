<?php


namespace App\Services;


use App\Salon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SalonTransactions extends Transactions
{
    public function setActive(Salon $salon = null): bool
    {
        if(!$salon) {
            $user = Auth::user();
            $salon = $user->salon;
        } else {
            $user = $salon->user;
        }

        if($salon) {
            $this->createTransaction(setting('salon.activate', 'prices', 0), null, 'Оплата за активацию салона', $user);
            $salon->active = 1;
            $salon->activated_at = Carbon::now();
            $salon->save();
            return true;
        }

        return false;
    }
}