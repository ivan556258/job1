<?php

namespace App\Services;

use App\City;
use App\Events\ProfileAdvertisingWasUpdated;
use App\ProfileAdvertising;
use App\ProfileAdvertisingSchedule;
use App\Scopes\ProfileHasImageScope;
use App\Scopes\ProfilesActiveScope;
use App\Scopes\ProfilesBlockScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Advertising
{
    //Получаем профили, которые платят по аукциону

    public function getProfiles(int $city_id = null): Collection
    {
        if (!$city_id) {
            $city = \App\Facades\Domain::getDomainCity();
            $city_id = $city->id;
        }

        if (Cache::has('top.profiles.city.' . $city_id)) {
           return Cache::get('top.profiles.city.' . $city_id);
        }

        return $this->setProfiles($city_id);
    }

    //Метод для записи в кэш профилей, учавствующих в аукционе. По одному или всем городам
    //$pay - снимать ли деньги с пользователя (true только для крона)
    public function setProfiles(int $city_id = null, bool $pay = false, bool $notificationNeed = true)
    {
        if (!$city_id) {
            $cities = City::all();
            foreach ($cities as $city) {
                $this->setProfilesByCity($city->id, $pay, $notificationNeed);
            }
        } else {
            return $this->setProfilesByCity($city_id, $pay, $notificationNeed);
        }

        return null;
    }

    //Метод получает пользователей по расписанию
    private function getProfilesBySchedule(int $city_id, \Closure $callback = null): Collection
    {
        $profiles = [];
        $advertisingProfiles = $this->getProfilesAdvertisingByCity($city_id);

        foreach ($advertisingProfiles as $advertisingProfile) {
            $profile = $advertisingProfile->profile()->withGlobalScope('ProfilesActiveScope', new ProfilesActiveScope)
                ->withGlobalScope('ProfilesBlockScope', new ProfilesBlockScope)
                ->withGlobalScope('ProfileHasImageScope', new ProfileHasImageScope)
                ->with(config('relations.girl-card'))->first();
            if(!$profile){
                $this->disableAdvertising($advertisingProfile);
                continue;
            }
            if ($profile->user->actualBalance() < $advertisingProfile->cost) {
                $this->disableAdvertising($advertisingProfile);
                continue;// return $this->getProfilesBySchedule($city_id);
            } else {
                if($callback) {
                    $callback($advertisingProfile);
                } else {
                    $profiles[] = $profile;
                }
            }
            unset($profile->user->balanceTransactions);
        }
        return collect($profiles);
    }

    //Получает ТОП профили по городу
    public function getProfilesAdvertisingByCity(int $city_id)
    {
        $timeProfiles = $this->getProfileAdvertisingSchedulesByCity($city_id);
        return ProfileAdvertising::topProfiles(data_get($timeProfiles->toArray(), '*.profile_id'))->get();
    }

    //Очистка кэша ТОП профилей
    public function clearCache(int $city_id = null)
    {
        if(!$city_id) {
            $cities = City::all();
            foreach ($cities as $city) {
                $this->clearCache($city->id);
            }
        } else {
            Cache::forget('top.profiles.city.' . $city_id);
        }
    }

    //Запись в кэш ТОП профилей
    private function setProfilesByCity(int $city_id, bool $pay = false, $notificationNeed = true)
    {
        $profiles = collect();
        if($pay) {
            $this->getProfilesBySchedule($city_id, function ($advertisingProfile) use($profiles, $notificationNeed) {
                $this->createTransaction($advertisingProfile, $notificationNeed);
                $profiles->add($advertisingProfile->profile);
            });

            //Событие, возникающее при обновлении списка ТОП профилей.
            // Уведомляет пользователей, что их позиция изменилась или что они больше не учавствуют в аукционе
            event(new ProfileAdvertisingWasUpdated($profiles, $this->getProfiles($city_id)));
        } else {
            $profiles = $this->getProfilesBySchedule($city_id);
        }

        Cache::put('top.profiles.city.' . $city_id, $profiles, config('general.advertising.time_for_refresh') * 1.5 * 60);

        return $profiles;
    }

    //Получает пользователей по расписанию в городе
    private function getProfileAdvertisingSchedulesByCity(int $city_id)
    {
        return ProfileAdvertisingSchedule::profilesByTime(Carbon::now()->timezone('Europe/Moscow')->format('H:m:s'),
            Carbon::now()->timezone('Europe/Moscow')->format('H:m:s'), Carbon::now()->dayOfWeek)
            ->whereHas('profile.user', function ($query) use ($city_id) {
                $query->where('city_id', $city_id);
            })->get();
    }

    //Удаляет профиль из БД рекламы, тем самым отключая ее
    private function disableAdvertising(ProfileAdvertising $profileAdvertising)
    {
        try {
            $profileAdvertising->delete();
        } catch (\Exception $e) {
            \Log::warning('Can\'t remove advertising profile', ['message' => $e->getMessage(), 'profile' => $profileAdvertising]);
        }
    }

    //Создает платеж за рекламу
    private function createTransaction(ProfileAdvertising $advertisingProfile, bool $notificationNeed = true)
    {
        $advertisingProfile->profile->transactions()->create([
            'amount' => -$advertisingProfile->cost,
            'amount_fact' => -$advertisingProfile->cost,
            'comment' => 'Оплата за размещение рекламы',
            'type' => 'expense',
            'user_id' => $advertisingProfile->profile->user_id,
            'notification_need' => $notificationNeed
        ]);
    }
}
