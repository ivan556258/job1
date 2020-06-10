<?php
namespace App\Exceptions;

use Illuminate\Support\Facades\Auth;
use Throwable;

class NotEnoughBalance extends \Exception
{
    private $amount;

    public function __construct($amount, $user = null, $message = "", $code = 0, Throwable $previous = null)
    {
        if(!$user && Auth::check()) {
            $user = Auth::user();
            $amount = $amount - $user->actualBalance();
        }

        $this->amount = $amount;

        \Log::debug('NotEnoughBalanceException', ['user' => $user, 'amount' => $amount, 'balance' => $user->actualBalance()]);
        parent::__construct($message, $code, $previous);
    }

    public function getAmount()
    {
        return $this->amount;
    }
}