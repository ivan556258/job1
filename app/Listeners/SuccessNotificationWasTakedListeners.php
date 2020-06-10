<?php


namespace App\Listeners;


use App\Transaction;
use Grechanyuk\FreeKassa\Events\SuccessNotificationWasTaked;

class SuccessNotificationWasTakedListeners extends Listener
{
    public function onSuccessNotificationWasTaked(SuccessNotificationWasTaked $event)
    {
        $notification = $event->getNotification();
        Transaction::whereKey($notification->getOrderId())
            ->update(['status' => 3, 'payment_method' => $notification->getCurrency()]);
    }
}