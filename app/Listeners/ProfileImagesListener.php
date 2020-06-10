<?php


namespace App\Listeners;


use App\Events\ProfileImagesWasModerated;

class ProfileImagesListener extends Listener
{
    public function onProfileImagesWasModerated(ProfileImagesWasModerated $event)
    {
        $profile = $event->getProfile();
        $profile->load(['user']);

        $notification = $profile->user->notificationSettings()->notificationStatus('imageModerationResult', 'email')->first();

        if($notification && $notification->active) {
            $profile->user->notify(new \App\Notifications\ProfileImagesWasModerated($profile));
        }
    }
}