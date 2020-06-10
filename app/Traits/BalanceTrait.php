<?php
namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait BalanceTrait
{
    //Баланс пользователя
    public function balance()
    {
        if(Cache::has('balance.' . $this->id)) {
        //    return Cache::get('balance.' . $this->id);
        }

        $balance = $this->actualBalance();

       // Cache::forever('balance.' . $this->id, $balance);
        return $balance;
    }

    //Баланс пользователя без кэша (Актуальный)
    public function actualBalance()
    {
        return $this->balanceTransactions->sum('amount_fact');
    }
}