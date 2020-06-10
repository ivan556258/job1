<?php

namespace App\Services;

use App\Events\ProfileNotVisibleNow;
use App\Facades\Advertising as AdvertisingProfile;
use App\Profile;
use Carbon\Carbon;

class ProfileTransactions extends Transactions
{
    //Ставит пометку оплачено (paid в БД)
    public function setPaid(Profile $profile, float $amount)
    {
        if ($amount > 0) {
            $this->createTransaction($amount, $profile->id, 'Плата за размещение анкеты #' . $profile->id, $profile->user);
        }
        $profile->paid = 1;
        $profile->save();
    }

    //Ставит пометку о том, что пользователь хочет ежемесячно платить за размещение
    // без проверочных фото. А также дату, когда он заплатил
    public function setVerification(Profile $profile, float $amount)
    {
        if ($amount) {
            $this->createTransaction($amount, $profile->id, 'Ежемесячная плата за активацию анкеты #' . $profile->id, $profile->user);
        }
        $profile->verification_paid = 1;
        $profile->verification_at = Carbon::now();
        $profile->save();
    }

    //Обновляет оплаты всех пользователей, включивших ежемесячную плату за размещение без подтв фото
    public function updateVerification()
    {
        $profiles = Profile::payVerification(-30)->get();
        foreach ($profiles as $profile) { 
			//echo $profile->user->actualBalance();
			//dd($profile);
            $verification_cost = setting($profile->user->getRole()->slug . '.verification_cost', 'prices');
            if ($profile->user->actualBalance() >= $verification_cost) {
                $this->setVerification($profile, $verification_cost);
            } else {
                $profile->verification_paid = 0;
				$profile->active = 0;
                $profile->save();
                event(new ProfileNotVisibleNow($profile,
                    'Недостаточно баланса для ежемесячной активации без проверочного фото'));
            }
			AdvertisingProfile::clearCache(); 
        }

        
    }
}