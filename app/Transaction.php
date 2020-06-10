<?php

namespace App;

use Grechanyuk\FreeKassa\Interfaces\PaymentInterface;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model implements PaymentInterface
{
    protected $fillable = [
        'user_id',
        'amount',
        'amount_fact',
        'payment_method',
        'status',
        'comment',
        'type',
        'notification_need'
    ];

    public function scopeIncomeTransactions($query)
    {
        $query->whereType('income');
    }

    public function scopeExpenseTransactions($query)
    {
        $query->whereType('expense');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function PaymentInterfaceGetOrderId()
    {
        return $this->id;
    }

    public function PaymentInterfaceGetAmount()
    {
        return $this->amount_fact;
    }

    public function PaymentInterfaceGetEmail()
    {
        return $this->user->email;
    }

    public function PaymentInterfaceGetPhone()
    {
        return telephone($this->user);
    }
}
