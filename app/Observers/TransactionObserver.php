<?php
namespace App\Observers;

use App\Notifications\Admin\NewIncome;
use App\Notifications\LowBalanceNotification;
use App\Notifications\NewExpense;
use App\Transaction;
use Illuminate\Support\Facades\Notification;

class TransactionObserver extends Observer
{
    public function created(Transaction $transaction)
    {
        $this->setBalance($transaction);
        $user = $transaction->user;
        if($user->city) {
            $country = $user->city->region->country;
            $notification = $user->notificationSettings()->notificationStatus('lowBalance', 'email')->first();

            if($user->actualBalance() <= config('notifications.lowBalanceValue') && $notification && $notification->active) {
                $user->notify(new LowBalanceNotification($user, $country));
            }
        }

        if($transaction->type === 'expense' && !empty($transaction->notification_need)) {
            dd(2);
            $user->notify(new NewExpense($transaction));
        } else if($transaction->type === 'income') {
            Notification::route('mail', config('app.adminNotificationEmail'))
                ->notify(new NewIncome($transaction));
        }

    }

    public function updated(Transaction $transaction)
    {
        $this->setBalance($transaction);
    }

    public function deleted(Transaction $transaction)
    {
        $this->setBalance($transaction);
    }

    private function setBalance(Transaction $transaction)
    {
        $this->clearCache('balance.' . $transaction->user_id);
    }
}
