<?php


namespace App\Services;


use App\City;
use App\Notifications\FreeProfilePositionDecreased;
use App\Profile;
use App\Scopes\ProfileHasImageScope;
use App\Scopes\ProfilesActiveScope;
use App\Scopes\ProfilesBlockScope;
use App\User;
use Illuminate\Support\Carbon;

class NotificationService
{
    //Вернет список уведолений, на которые можно подписаться
    public function notifications()
    {
        return [
            'channels' => [
                'email',
                'system'
            ],

            'items' => [
                'news' => 'Получать новости сайта и рассылку',
                'reviewAnswer' => 'Уведомлять об ответах на мои комментарии',
                'ticketAnswer' => 'Уведомлять о новых ответах в тикетах',
                'lowBalance' => 'Уведомлять о низком балансе',
                'newReview' => 'Уведомлять о новых отзывах',
                'imageModerationResult' => 'Уведомлять о результатах модерации фотографий',
                'worsePaidProfilePosition' => 'Уведомлять о понижении позиций платных анкет',
                'worseFreeProfilePosition' => 'Уведомлять о понижении позиций бесплатных анкет',
            ],

            'active' => 1
        ];
    }

    //Записывает в БД пункты, выбранные пользователем. (какие уведомления слать)
    public function setData(array $notificationsData = [], User $user = null)
    {
        if (!$user && \Auth::check()) {
            $user = \Auth::user();
        }

        if ($user) {
            if (!$notificationsData) {
                $notifications = $this->notifications();

                foreach ($notifications['items'] as $key => $notification) {
                    foreach ($notifications['channels'] as $channel) {
                        $notificationsData[] = [
                            'notification' => $key,
                            'channel' => $channel,
                            'active' => $notifications['active']
                        ];
                    }
                }

            }

            foreach ($notificationsData as $notification) {
                $user->notificationSettings()->updateOrCreate([
                    'notification' => $notification['notification'],
                    'channel' => $notification['channel']
                ], $notification);
            }
        }
    }

    //Отправляет всем пользователям информацию о том, что их позиция ТОП профилей изменилась
    public function sendNotificationAboutPositionDecrease()
    {
        Profile::addGlobalScope(new ProfileHasImageScope);
        Profile::addGlobalScope(new ProfilesActiveScope);
        Profile::addGlobalScope(new ProfilesBlockScope);
        $cities = City::all();

        foreach ($cities as $city) {
            $newProfiles = Profile::whereActivatedAt(Carbon::today())
                ->whereHas('user', function ($query) use ($city) {
                    $query->where('users.city_id', $city->id);
                })->exists();

            if ($newProfiles) {
                $allProfileUsers = Profile::where(function ($query) {
                    $query->whereNull('activated_at')
                        ->orWhere('activated_at', '!=', Carbon::today());
                })->whereHas('user', function ($query) use ($city) {
                    $query->where('users.city_id', $city->id);
                })->with(['user.notificationSettings'])->groupBy('user_id')->get();

                foreach ($allProfileUsers as $profile) {
                    if($profile->user->notificationSettings->where('notification', 'worseFreeProfilePosition')->where('channel', 'email')->first()) {
                        $profile->user->notify(new FreeProfilePositionDecreased($profile->user));
                    }
                }
            }
        }
    }
}
