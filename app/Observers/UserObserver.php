<?php


namespace App\Observers;


use App\Facades\NotificationServiceFacade;
use App\User;

class UserObserver
{
    public function created(User $user)
    {
        NotificationServiceFacade::setData([], $user);
    }
}