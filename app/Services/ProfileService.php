<?php

namespace App\Services;

use App\Convenience;
use App\Education;
use App\Events\ImageDeleted;
use App\Events\ProfileImagesWasModerated;
use App\Events\VideoDeleted;
use App\Facades\ProfileTransactions as ProfileTransactionsService;
use App\Profile;
use App\Service;
use App\User;
use App\WorkTime;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Facades\Advertising as AdvertisingFacade;

class ProfileService
{
    public function applyFilters(Request $request, $profiles)
    {
        //TODO Refactor crutch
        return \App\Facades\ProfileFilters::applyFilters($request, $profiles);
    }

    public function getFiltersVariables(Request $request): array
    {
        //TODO Refactor crutch
        return \App\Facades\ProfileFilters::getFiltersVariables($request);
    }

    //Записывает в БД связанные данные профиля
    public function createExtraData(Request $request, Profile $profile)
    {
        $profileImages = $profile->images;
        $profileImagesWasModerated = false;
        if ($request->has('images')) {
            foreach ($request->input('images') as $item) {
                $profileImage = $profile->images()->updateOrCreate([
                    'image' => $item['image']
                ], $item);

                if ($profileImg = $profileImages->where('id', $profileImage->id)->first()) {
                    if ($item['active'] != $profileImg->active || (!empty($item['moderate_message']) && $item['moderate_message'] != $profileImg->moderate_message)) {
                        $profileImagesWasModerated = true;
                    }
                }
            }
        }

        //Если изображения прошли или не прошли модерацию - шлет уведомление на почту
        if ($profileImagesWasModerated && Auth::check() && Auth::user()->level() > 3) {
            event(new ProfileImagesWasModerated($profile));
        }

        //Оплата профиля (paid колонка в БД)
        if (Auth::check() && Auth::user()->level() > 3 && $profile->active && !$profile->paid) {
            ProfileTransactionsService::setPaid($profile, 0);
        }

        //Сохраняет проверочные фото
        if ($request->has('verification_images')) {
            foreach ($request->input('verification_images') as $item) {
                $profile->verification_images()->updateOrCreate([
                    'image' => $item['image']
                ], $item);
            }
        }

        //Сохраняет проверочные видео
        if ($request->has('videos')) {
            foreach ($request->input('videos') as $item) {
                $profile->videos()->updateOrCreate([
                    'video' => $item['video']
                ], $item);
            }
        }

        //Удаляет изображения и отправляет ивент, чтобы удалить файлы
        if ($request->input('images_deleting')) {
            foreach (explode(',', $request->input('images_deleting')) as $item) {
                $image = $profile->images()->whereKey($item);
                event(new ImageDeleted($image->first()->image));
                $image->delete();
            }
        }

        //Удаляет видео и отправляет ивент, чтобы удалить файлы
        if ($request->input('videos_deleting')) {
            foreach (explode(',', $request->input('videos_deleting')) as $item) {
                $video = $profile->videos()->whereKey($item);
                event(new VideoDeleted($video->first()->video));
                $video->delete();
            }
        }

        //Удаляет проверочные фото и отправляет ивент, чтобы удалить файлы
        if ($request->input('verification_images_deleting')) {
            foreach (explode(',', $request->input('verification_images_deleting')) as $item) {
                $image = $profile->verification_images()->whereKey($item);
                event(new ImageDeleted($image->first()->image));
                $image->delete();
            }
        }

        //Пересохраняем рабочее расписание
        $profile->work_schedules()->delete();
        $workSchedules = Arr::where($request->input('work_schedule'), function ($value, $key) {
            return !empty($value['time_from']) || !empty($value['time_to']);
        });
        foreach ($workSchedules as $day => $item) {
            $item['day'] = $day;
            $profile->work_schedules()->create($item);
        }

        //Пересохраняем расценки
        $profile->prices()->delete();
        foreach ($request->input('prices') as $type => $prices) {
            foreach ($prices as $price) {
                if (is_array($price) && $price['price']) {
                    $price['type'] = $type;
                    $profile->prices()->create($price);
                }
            }
        }

        //Пересохраняем расписание рекламы профиля
        $profile->advertisingSchedule()->delete();
        $advertisingSchedule = false;
        if ($request->input('advertising_schedule')) {
            foreach ($request->input('advertising_schedule') as $days) {
                foreach ($days as $day) {
                    if (isset($day['day'])) {
                        $day['city_id'] = $profile->user->city_id;
                        $profile->advertisingSchedule()->create($day);
                        $advertisingSchedule = true;
                    }
                }
            }
        }

        //Пересохраняем позиции рекламы профиля
        $profile->advertising()->delete();
        if ($request->input('advertising') && $advertisingSchedule) {
            $advs = Arr::where($request->input('advertising', []), function ($value, $key) {
                return (float)$value['cost'] > 0;
            });

            foreach ($advs as $adv) {
                $adv['cost'] = convertCurrency($adv['cost'], null, false, true);
                $profile->advertising()->create($adv);
            }
        }

        //Пересохраняем с кем работает профиль (Мужчины, Бабы, дети)
        $profile->clients()->delete();
        $profile->clients()->create($request->all());

        //Сохраняем индивидуальные услуги
        if ($request->input('individual_service.name') && $request->input('individual_service.price')) {
            $profile->individual_service()->updateOrCreate([], $request->input('individual_service'));
        } else {
            $profile->individual_service()->delete();
        }

        //Синхронизируем услуги
        $profile->services()->sync($request->input('services'));

        //Синхронизируем удобства
        $profile->conveniences()->sync($request->input('conveniences'));

        //Синхронизируем метро
        $profile->metros()->sync($request->input('metros') ? Arr::pluck($request->input('metros'),
            ['metro_id']) : null);

        //Пересохраняем настройки публикации
        $profile->publishes()->updateOrCreate([], $request->input('publishes'));

        //Создаем атрибуты пользователя, если они не были добавлены ранее
        $userAttributes = [];

        //Сохраним телефон, если он не был добавлен
        $userTelephoneAttributes = [];

        $userTelephones = $profile->user->telephones;
        $telephoneCode = $request->input('telephone_code', false);
        $telephone = $request->input('telephone', false);
        if($telephone && $telephoneCode) {
            $currentTelephone = $profile->user
                ->hasTelephone($telephoneCode, $telephone);
            if (!$currentTelephone) {
                $userTelephoneAttributes = [
                    'telephone' => $telephone,
                    'telephone_code' => $telephoneCode,
                    'telephone_confirmed' => 0
                ];
            } else {
                $userTelephone = $userTelephones->where('telephone', $telephone)
                    ->where('telephone_code', $telephoneCode)->first();
            }
        }


        if ($request->has('address') && $profile->user->hasRole('salon') && empty($profile->user->salon->address) && !empty($profile->user->salon)) {
            $salon = $profile->user->salon;
            $salon->update([
                'address' => $request->input('address')
            ]);
        }

        if (empty($profile->user->city_id)) {
            $userAttributes['city_id'] = $request->input('city_id');
        }

        //Сохраняем атрибуты пользователя, если они не были добавлены ранее
        if ($userAttributes) {
            $profile->user()->update($userAttributes);
        }

        if ($userTelephoneAttributes) {
            $userTelephone = $profile->user->telephones()->create($userTelephoneAttributes);
        }

        if (isset($userTelephone) && $profile->telephone_id !== $userTelephone->id) {
            $profile->telephone_id = $userTelephone->id;
            $profile->save();
        }
    }

    //Получаем переменные для формы создания/редактирования профиля
    public function getFormVariables(Request $request = null, Profile $profile = null): array
    {
        $statistic = $this->getSplitting($profile->statistic ?? collect());
        $expenses = $this->getSplitting($profile->expenses ?? collect());
        $currentAdvertisingProfiles = $this->currentAdvertisingProfiles($profile);
        return [
            'educations' => Education::all(),
            'work_times' => WorkTime::all(),
            'user' => Auth::user()->load(['city', 'city.region', 'city.region.country']),
            'services' => Service::all()->groupBy('type'),
            'userRole' => $profile ? $profile->user->getRole() : Auth::user()->getRole(),
            'conveniences' => Convenience::all(),
            'activeSection' => $request ? ($request->input('section') ?: 'general') : 'general', //Переход на определенную вкладку формы
            'expenses' => array_merge($expenses, [ //Расходы
                'client_cost' => [
                    'today' => $statistic['today']->count() ? abs($expenses['today']->sum('amount_fact')) / $statistic['today']->count() * 100 : '-',
                    'yesterday' => $statistic['yesterday']->count() ? abs($expenses['yesterday']->sum('amount_fact')) / $statistic['yesterday']->count() * 100 : '-',
                    'week' => $statistic['week']->count() ? abs($expenses['week']->sum('amount_fact')) / $statistic['week']->count() * 100 : '-',
                    'month' => $statistic['month']->count() ? abs($expenses['month']->sum('amount_fact')) / $statistic['month']->count() * 100 : '-'
                ],
                'client_count' => [
                    'today' => $statistic['today']->count() / 100,
                    'yesterday' => $statistic['yesterday']->count() / 100,
                    'week' => $statistic['week']->count() / 100,
                    'month' => $statistic['month']->count() / 100
                ]
            ]),
            'statistic' => array_merge($statistic, [ //Статистика
                'hourly' => $profile ? DB::select('SELECT DATE_FORMAT(created_at,\'%H\') AS hour, COUNT(DATE_FORMAT(created_at,\'%H\')) AS views, (SELECT COUNT(DISTINCT fingerprint) FROM `profile_statistics` WHERE `profile_statistics`.`profile_id` = ' . $profile->id . ' AND `profile_statistics`.`profile_id` IS NOT NULL AND DATE_FORMAT(created_at,\'%H\') = hour AND created_at BETWEEN "' . Carbon::today() . '" AND "' . Carbon::now() . '") as visitors FROM `profile_statistics` WHERE `profile_statistics`.`profile_id` = ' . $profile->id . ' AND `profile_statistics`.`profile_id` IS NOT NULL AND created_at BETWEEN "' . Carbon::today() . '" AND "' . Carbon::now() . '" GROUP BY DATE_FORMAT(created_at,\'%H\')') : collect(),
                'monthly' => $profile ? DB::select('SELECT DATE_FORMAT(created_at,\'%Y,%m,%d\') AS day, COUNT(DATE_FORMAT(created_at,\'%Y,%m,%d\')) AS views, (SELECT COUNT(DISTINCT fingerprint) FROM `profile_statistics` WHERE `profile_statistics`.`profile_id` = ' . $profile->id . ' AND `profile_statistics`.`profile_id` IS NOT NULL AND DATE_FORMAT(created_at,\'%Y,%m,%d\') = day AND created_at BETWEEN "' . Carbon::now()->add(-1,
                        'month') . '" AND "' . Carbon::now() . '") as visitors FROM `profile_statistics` WHERE `profile_statistics`.`profile_id` = ' . $profile->id . ' AND `profile_statistics`.`profile_id` IS NOT NULL AND created_at BETWEEN "' . Carbon::now()->add(-1,
                        'month') . '" AND "' . Carbon::now() . '" GROUP BY DATE_FORMAT(created_at,\'%Y,%m,%d\')') : collect()
            ]),
            'advertisingProfileCosts' => $currentAdvertisingProfiles, //Стоимости анкет, учавствующих в аукционе этого города
            'advertisingSchedule' => $profile ? $this->advertisingSchedule($profile) : [], //Раписание рекламы пользователя
            'workSchedules' => $profile ? $this->workSchedules($profile) : [] //Расписание рабочего времени пользователя
        ];
    }

    //Вернет переменные для страницы show
    public function getShowVariables(Profile $profile): array
    {
        $work_schedules = $this->workSchedules($profile);
        //TODO Временная зона пользователя

        return [
            'meta_title' => $profile->meta_title ?? $profile->name . ' ,' . ageDictionary(date('Y') - $profile->birth) . ', г.' . $profile->user->city->name . ', #' . $profile->id,
            'meta_description' => $profile->meta_description ?? $profile->description,
            'meta_keywords' => $profile->meta_keywords ?? '',
            'reviews' => $this->reviews($profile),
            'commentsCount' => $profile->comments()->count(),
            'reviewsCount' => $profile->reviews()->count(),
            'profile' => $profile,
            'work_schedules' => $work_schedules, //Расписание работы
            'work_schedule' => [ //Работает ли сегодня и сейчас
                'today' => $work_schedules[Carbon::now()->dayOfWeek] ?? false,
                'now' => $this->workNow($work_schedules)
            ],
            'services' => [
                'service' => $profile->services ? $profile->services->groupBy('type') : [],
                'individual' => $profile->individual_service ?: []
            ],
            'similarProfiles' => $this->getSimilarProfiles($profile), //Похожие профили
        ];
    }

    //Вернет переменные для предпросмотра профиля после добавления
    public function getPreviewVariables(Profile $profile): array
    {
        $showVariables = $this->getShowVariables($profile);

        $data['preview'] = true;
        $data['cost'] = $this->getCostOfProfiles(Auth::user()->profiles()->count());
        $data['verification_cost'] = setting(Auth::user()->getRole()->slug . '.verification_cost', 'prices');

        return array_merge($showVariables, $data);
    }

    public function getCostOfProfiles(int $profilesCount): float
    {
        $cost = 0;
        $costOfProfiles = setting(Auth::user()->getRole()->slug . '.profiles', 'prices');
        if ($costOfProfiles) {
            if (isset($costOfProfiles[$profilesCount])) {
                $cost = $costOfProfiles[$profilesCount];
            } else {
                $cost = last($costOfProfiles);
            }
        }

        return $cost;
    }

    //Вернет отзывы вместе с комментариями
    public function reviews(Profile $profile, bool $withReviews = true, bool $withComments = true)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $comments = collect([]);
        $reviews = collect([]);
		
        if ($withComments) {
            $comments = $profile->comments()->root()->with(['answers', 'user'])->get();
        }

        if ($withReviews) {
            $reviews = $profile->reviews()->with(['answer', 'user'])->get();
        }

        $items = $reviews->merge($comments)->sortByDesc('created_at');
        $currentItems = $items->slice($perPage * ($currentPage - 1), $perPage);

        return new LengthAwarePaginator($currentItems, $items->count(), $perPage, $currentPage);
    }

    //Получает новейшие профили
    public function getNewestProfiles()
    {
        $city = \App\Facades\Domain::getDomainCity();
        if (Cache::has('profiles.newest.city.' . $city->id)) {
            return Cache::get('profiles.newest.city.' . $city->id);
        }

        $profiles = Profile::with(config('relations.profiles.index'))->onlyNew()->get();
        Cache::put('profiles.newest.city.' . $city->id, $profiles, 3600);

        return $profiles;
    }

    public function getCountOfProfiles(string $userRole = 'individual')
    {
        $city = \App\Facades\Domain::getDomainCity();
        if (Cache::has('profiles.count.city.' . $city->id)) {
            return Cache::get('profiles.count.city.' . $city->id);
        }

        $count = Profile::whereHas('user.roles', function ($query) use ($userRole) {
            $query->where('roles.slug', $userRole);
        })->count();
        Cache::put('profiles.count.city.' . $city->id, $count, 3600);

        return $count;
    }

    private function workNow(array $work_schedules): bool
    {
        $now = false;
        if (!empty($work_schedules[Carbon::now('Europe/Moscow')->dayOfWeek])) {
            if ($work_schedules[Carbon::now('Europe/Moscow')->dayOfWeek]['time_from'] < Carbon::now('Europe/Moscow')->format('H:i')) {
                if ($work_schedules[Carbon::now('Europe/Moscow')->dayOfWeek]['time_to'] > Carbon::now('Europe/Moscow')->format('H:i')) {
                    $now = true;
                }

                if ($work_schedules[Carbon::now('Europe/Moscow')->dayOfWeek]['time_to'] < $work_schedules[Carbon::now('Europe/Moscow')->dayOfWeek]['time_from']) {
                    if ($work_schedules[Carbon::now('Europe/Moscow')->dayOfWeek]['time_to'] < Carbon::now('Europe/Moscow')->format('H:i')) {
                        $now = true;
                    }
                }
            }

            if ($work_schedules[Carbon::now('Europe/Moscow')->dayOfWeek]['time_from'] === $work_schedules[Carbon::now('Europe/Moscow')->dayOfWeek]['time_to']) {
                $now = true;
            }
        }

        return $now;
    }

    private function getSimilarProfiles(Profile $profile): Collection
    {
        $profileCityId = $profile->user->city_id;
        $users = User::whereCityId($profileCityId)->whereHas('profiles')->inRandomOrder()->limit(4)->get();
        if ($users) {
            return Profile::whereIn('user_id', data_get($users, '*.id'))
                ->where('id', '!=', $profile->id)
                ->with(config('relations.girl-card'))->inRandomOrder()->limit(4)->get();
        }

        return collect();
    }

    private function workSchedules(Profile $profile): array
    {
        $arr = [];
        if ($profile->work_schedules) {
            foreach ($profile->work_schedules as $work_schedule) {
                $arr[$work_schedule->day] = [
                    'time_from' => $work_schedule->time_from ? Carbon::createFromTimeString($work_schedule->time_from)->format('H:i') : null,
                    'time_to' => $work_schedule->time_to ? Carbon::createFromTimeString($work_schedule->time_to)->format('H:i') : null
                ];
            }
        }

        return $arr;
    }

    private function advertisingSchedule(Profile $profile): array
    {
        $arr = [];
        if ($profile->advertisingSchedule) {
            foreach ($profile->advertisingSchedule as $schedule) {
                $arr[$schedule->day][Carbon::createFromTimeString($schedule->time_from)->format('G')] = true;
            }
        }

        return $arr;
    }

    private function currentAdvertisingProfiles(Profile $profile = null): ?Collection
    {
        $city_id = $profile ? $profile->user->city_id : Auth::user()->city_id;

        return $city_id ? AdvertisingFacade::getProfilesAdvertisingByCity($city_id)->load(['profile']) : null;
    }

    //Статистика или расходы за сегодня, вчера, неделю, месяц
    private function getSplitting(Collection $collection): array
    {
        return [
            'today' => $collection ? $collection->whereBetween('created_at',
                [Carbon::today(), Carbon::now()]) : collect(),
            'yesterday' => $collection ? $collection->whereBetween('created_at',
                [Carbon::yesterday(), Carbon::today()]) : collect(),
            'week' => $collection ? $collection->whereBetween('created_at',
                [Carbon::now()->add(-1, 'week'), Carbon::now()]) : collect(),
            'month' => $collection ? $collection->whereBetween('created_at',
                [Carbon::now()->add(-1, 'month'), Carbon::now()]) : collect()
        ];
    }
}
