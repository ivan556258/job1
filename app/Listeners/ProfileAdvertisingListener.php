<?php


namespace App\Listeners;


use App\Events\ProfileAdvertisingWasUpdated;
use App\Notifications\PaidProfileAdvertisingWasDisabled;
use App\Notifications\PaidProfilePositionDecreased;

class ProfileAdvertisingListener extends Listener
{
    public function onProfileAdvertisingWasUpdated(ProfileAdvertisingWasUpdated $event)
    {
        $newProfiles = $event->getNewProfiles();
        $oldProfiles = $event->getOldProfiles();

        foreach ($oldProfiles as $oldIndex => $oldProfile) {
            if($oldProfile->user->notificationSettings()->notificationStatus('worsePaidProfilePosition', 'email')->first()) {
                if($newProfiles->where('id', $oldProfile->id)->isNotEmpty()) {
                    foreach ($newProfiles->where('id', $oldProfile->id) as $newIndex => $newProfile) {
                        if($oldIndex < $newIndex) {
                            $oldProfile->user->notify(new PaidProfilePositionDecreased($oldProfile, $newIndex));
                        }
                    }
                } else {
                    $oldProfile->user->notify(new PaidProfileAdvertisingWasDisabled($oldProfile));
                }
            }
        }
    }
}