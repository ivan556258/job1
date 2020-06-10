<?php


namespace App\Facades;


use App\User;
use Illuminate\Support\Facades\Facade;

/**
 * @method static notifications()
 * @method static setData(array $notifications = [], User $user = null)
 * @method static sendNotificationAboutPositionDecrease()
 */
class NotificationServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'notification_service';
    }
}