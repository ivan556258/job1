<?php


namespace App\Services;


use App\City;
use App\Country;
use App\Events\BannerWasBought;
use App\Exceptions\NotEnoughBalance;
use App\Facades\SalonTransactions as SalonTransactionsAlias;
use \App\Facades\ProfileTransactions;
use App\Profile;
use App\Salon;
use App\User;
use App\AdvertisingBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SalonAdvertising
{
    public static $CACHE_NAME = 'availableDaysForSalonBanners.';

    /**
     * Поднимает салон в списке выше всех остальных
     * @return string
     * @throws NotEnoughBalance
     */
    public function rise()
    {
        if (Auth::user()->actualBalance() < setting('salon.rise', 'prices')) {
            throw new NotEnoughBalance(setting('salon.rise', 'prices'));
        }

        SalonTransactionsAlias::createTransaction(setting('salon.rise', 'prices'), null, 'Оплата за поднятие салона');

        Auth::user()->salon->update([
            'elevated_at' => Carbon::now()
        ]);

        return Auth::user()->salon->elevated_at;
    }

    /**
     * Добавляет новый рекламный баннер
     * @param Request $request
     * @param User|null $user
     * @throws NotEnoughBalance
     */
    public function buyBanner(Request $request, User $user = null)
    {
        $data = $request->only('adv');
        if (!$user) {
            $user = Auth::user();
        }

        if(empty($data['adv'])) {
            \Log::warning('Advertising banner did n\'t saved');
            die();
        }

        foreach ($data['adv'] as $item) {
            if($item['type'] === 'city') {
                $amount = setting($user->getRole()->slug.'.index_banner', 'prices');
            } else {
                $amount = setting($user->getRole()->slug.'.index_banner_country', 'prices');
            }

            if ($user->actualBalance() < $amount) {
                throw new NotEnoughBalance($amount, $user);
            }

            $date_start = Carbon::createFromFormat('d.m.Y', $item['date_start']);

            $mergeData = [
                'active' => 0,
                'city_id' => $item['type'] === 'city' ? $user->city_id : null,
                'country_id' => $item['type'] === 'country' ? $user->city->region->country_id : null,
                'date_start' => $date_start,
                'date_finish' => $date_start->copy()->addDays($item['days']),
                'user_id' => $user->id
            ];


            if(!empty($item['image_banner'])) {
                $image = $item['image_banner']->store('/files/users/' . $user->id, 'publicFolder');
                $mergeData = array_merge($mergeData, ['image' => $image]);
            }


            if($user->hasRole('salon')) {
                $advertising = $user->salon;
            } else {
                $advertising = Profile::whereKey($item['profile_id'])->first();
            }

            $advertising = $advertising->advertisingBanners();

            $advertising->updateOrCreate([
                'id' => $item['id']
            ], array_merge($item, $mergeData));

            event(new BannerWasBought($advertising));
        }
    }

    //Получает рекламные баннеры
    public function getBanners()
    {
        $city = \App\Facades\Domain::getDomainCity();

        if (Cache::has('banners.salon.city.' . $city->id)) {
            return Cache::get('banners.salon.city.' . $city->id);
        }

        return $this->setBanners($city->id);
    }

    //Записывает рекламные баннеры в кэш ($pay - нужно ли снимать деньги. true только в кроне)
    public function setBanners(int $city_id = null, bool $pay = false)
    {
        Log::debug('Set banners start');
        if($pay && $city_id === null) {
            AdvertisingBanner::where('paid', 1)->update([
                'paid' => 0
            ]);
        }

        if (!$city_id) {
            Log::debug('City not found. All city run');
            $cities = City::all();
            foreach ($cities as $city) {
                $this->setBanners($city->id, $pay);
            }
        } else {
            $city = City::whereKey($city_id)->first();
            Log::debug('City ' . $city->name);
            if(!$city) {
                return null;
            }

            $banners = AdvertisingBanner::today($city_id, $city->region->country->id)->whereActive(1)->get();
            Log::debug('Banners found.', [
                'banners' => $banners->toArray()
            ]);

            $actualBanners = [];

            foreach ($banners as $banner) {
                if($pay) {
                    Log::debug('Pay need');
                    if($banner->city_id) {
                        $amount = setting($banner->user->getRole()->slug.'.index_banner', 'prices');
                    } else {
                        $amount = setting($banner->user->getRole()->slug.'.index_banner_country', 'prices');
                    }

                    Log::debug('Amount is: ' . $amount);

                    if ($banner->user->actualBalance() >= $amount || $banner->paid) {
                        Log::debug('User can buy banner');
                        if(!$banner->paid) {
                            if($banner->bannerable instanceof Salon) {
                                Log::debug('Salon banner');
                                SalonTransactionsAlias::createTransaction($amount, null, 'Оплата за размещение баннера', $banner->user);
                            } elseif ($banner->bannerable instanceof Profile) {
                                Log::debug('Profile banner');
                                ProfileTransactions::createTransaction($amount, $banner->bannerable->id, 'Оплата за размещение баннера', $banner->user);
                            }

                            $banner->paid = 1;
                            $banner->save();
                        }

                        $actualBanners[] = $banner;
                    } else {
                        Log::debug('User can\'t buy banner');
                        $banner->active = 0;
                        $banner->save();
                    }
                } else {
                    $actualBanners[] = $banner;
                }
            }

            if($pay) {
                Cache::forget('banners.salon.city.' . $city_id);
            } else {
                Cache::put('banners.salon.city.' . $city_id, $actualBanners, 24 * 60 * 60);
            }
            Log::debug('Buy banners finished. Actual banners are', [
                'actualBanners' => $actualBanners
            ]);

            return $actualBanners;
        }

        Log::debug('Buy banners finished. Banners are null');
        return null;
    }

    //Получает список дней, в которые нельзя размещать баннер, т.к. все места заняты
    public function getUnAvailableDays($location, int $advertisingBannerId = null)
    {
        if ($location instanceof City) {
            AdvertisingBanner::where('city_id', $location->id)
                ->orWhere('country_id', $location->region->country->id);
        } else if ($location instanceof Country) {
            AdvertisingBanner::where('country_id', $location->id)
                ->orWhereIn('city_id', data_get($location->cities, '*.id'));
        }

        $unAvailableDays = AdvertisingBanner::where('date_finish', '>', Carbon::today());

        if($advertisingBannerId) {
            $unAvailableDays->whereKeyNot($advertisingBannerId);
        }

        $unAvailableDays = $unAvailableDays->get();

        $days = [];

        foreach ($unAvailableDays as $unAvailableDay) {
            foreach(Carbon::range($unAvailableDay->date_start, $unAvailableDay->date_finish) as $day) {
                $days[] = $day->format('d.m.Y');
            }
        }

        $days = Arr::where(array_count_values($days), function ($value, $key) {
            return $value >= 3; //3 - число максимально возможным баннеров
        });

        [$days, $counts] = Arr::divide($days);

        if(Auth::user()->level() < 3) {
            $days[] = Carbon::today()->format('d.m.Y');
        }

        usort($days, ['SalonAdvertising', 'date_sort']);

        return $days;
    }

    public function getLastUnavailableDay($location):? string
    {
        $result = null;
        $days = $this->getUnAvailableDays($location);
        foreach ($days as $day) {
            $next = next($days);
            if($next) {
                $next = Carbon::createFromFormat('d.m.Y', $next);
                $current = Carbon::createFromFormat('d.m.Y', $day);
                if($current->diff($next)->days > 1) {
                    $result = $current->format('d.m.Y');
                    break;
                }
            } else {
                $result = $day;
            }
        }

        return $result;
    }

    public function date_sort($a, $b) {
        return strtotime($a) - strtotime($b);
    }
}
