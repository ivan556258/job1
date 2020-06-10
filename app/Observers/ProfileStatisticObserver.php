<?php
namespace App\Observers;

use App\ProfileStatistic;

class ProfileStatisticObserver
{
    public function creating(ProfileStatistic $profileStatistic)
    {
        $oldStatistic = ProfileStatistic::whereFingerprint($profileStatistic->fingerprint)->first();
        if(!$oldStatistic) {
            $profileStatistic->profile->increment('views');
        }
    }
}