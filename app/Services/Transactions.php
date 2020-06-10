<?php


namespace App\Services;


use App\User;
use Illuminate\Support\Facades\Auth;

class Transactions
{
    public function createTransaction(float $amount, int $profileId = null, string $comment = '', User $user = null)
    {
        if(!$user) {
            $user = Auth::user();
        }

        $user->transactions()->create([
            'amount' => -$amount,
            'amount_fact' => -$amount,
            'profile_id' => $profileId,
            'type' => 'expense',
            'comment' => $comment
        ]);
    }
}