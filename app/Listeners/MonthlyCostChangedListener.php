<?php


namespace App\Listeners;


use App\Events\MonthlyCostWasChanged;
use App\User;
use \App\Notifications\MonthlyCostWasChanged as MonthlyCostWasChangedNotification;

class MonthlyCostChangedListener extends Listener
{
    public function onMonthlyCostWasChanged(MonthlyCostWasChanged $event)
    {
        $users = User::whereHas('roles', function ($query) use($event) {
            $query->where('slug', $event->getUserRoleSlug());
        })->whereHas('profiles', function ($query) {
            $query->where('verification_paid', 1);
        })->get();

        foreach ($users as $user) {
            $user->notify(new MonthlyCostWasChangedNotification($user, $event->getNewCost()));
        }
    }
}