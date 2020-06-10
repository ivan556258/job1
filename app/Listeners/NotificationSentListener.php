<?php


namespace App\Listeners;


use App\Notifications\Admin\AdminNotification;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Notification;

class NotificationSentListener extends Listener
{
    public function onNotificationSent(NotificationSent $event)
    {
        $notification = $event->notification;

        if(method_exists($notification, 'getAdminDescription')) {
            Notification::route('mail', config('app.adminNotificationEmail'))
                ->notify(new AdminNotification($notification));
        }
    }
}
