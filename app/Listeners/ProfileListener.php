<?php

namespace App\Listeners;

use App\Events\ProfileNotVisibleNow;
use App\Events\ProfileWasBlocked;
use App\Notifications\ProfileNotVisibleNow as ProfileNotVisibleNowNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use \App\Notifications\ProfileWasBlocked as ProfileWasBlockedNotification;

class ProfileListener extends Listener
{
    public function onProfileWasBlocked(ProfileWasBlocked $event)
    {
        $profile = $event->getProfile()->load(['user']);

        $profile->user->notify(new ProfileWasBlockedNotification($profile));
    }

    public function onProfileNotVisibleNow(ProfileNotVisibleNow $event)
    {
        $profile = $event->getProfile()->load(['user']);

        $profile->user->notify(new ProfileNotVisibleNowNotification($profile, $event->getReason()));
    }
}
