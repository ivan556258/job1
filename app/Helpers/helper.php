<?php

use App\Profile;
use App\User;
use App\UserTelephone;
use Illuminate\Pagination\LengthAwarePaginator;

if (!function_exists('setting')) {
    function setting(string $key, string $type = 'general', $default = null)
    {
        if (\Illuminate\Support\Facades\Cache::has('settings')) {
            $settings = \Illuminate\Support\Facades\Cache::get('settings');
        } else {
            $settings = \App\Setting::all();
            \Illuminate\Support\Facades\Cache::forever('settings', $settings);
        }
		//dd($settings);
        $key = explode('.', $key);
        $setting = $settings->where('set_type', $type)->where('set_key', $key[0])->first();
		
        if ($setting) {
            if(@unserialize($setting->set_value) === false) {
                return $setting->set_value;
            } else {
                $data = unserialize($setting->set_value);
                if(count($key) > 1) {
                    unset($key[0]);
                    $data = data_get($data, implode('.', $key));
                }
				
                if($data) {
                    return $data;
                }
            }
        }
		
        return $default;
    }
}

if (!function_exists('sideBarActive')) {
    function sideBarActive(string $url)
    {
        $currentUrl = \Illuminate\Support\Facades\URL::current();
        return $currentUrl == $url || strpos($currentUrl, $url) === 0 ? ' active' : '';
    }
}

if (!function_exists('breadcrumbs')) {
    function breadcrumbs(array $models = [])
    {
        return \DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs::render(Route::current()->getName(), $models);
    }
}

if (!function_exists('strToArray')) {
    function strToArray(string $string)
    {
        return explode(PHP_EOL, $string);
    }
}

if (!function_exists('girlSticker')) {
    function girlSticker()
    {
        $colorSettings = config('general.girlCardStickerBackgrounds');
        if ($colorSettings) {
            $count = count($colorSettings);

            return $count ? implode(' ', $colorSettings[rand(0, $count - 1)]) : null;
        }

        return null;
    }
}

if (!function_exists('isAccount')) {
    function isAccount()
    {
        return strpos(\Illuminate\Support\Facades\Route::currentRouteName(), 'account') === 0;
    }
}

if (!function_exists('isMyMessage')) {
    function isMyMessage(int $user_id)
    {
        return \Illuminate\Support\Facades\Auth::id() === $user_id;
    }
}

if (!function_exists('ticketHasNewMessage')) {
    function ticketHasNewMessage(\App\Ticket $ticket)
    {
        if (Auth::check() && $ticket->lastMessage) {
            return $ticket->lastMessage->user->id !== Auth::id();
        }

        return false;
    }
}

if (!function_exists('paginationCount')) {
    function paginationCount(LengthAwarePaginator $paginator): string
    {
        $txt = $txt = 1 + ($paginator->currentPage() - 1) * $paginator->perPage() . ' - ';

        if (!$paginator->total()) {
            $txt = '0 - 0';
        } else {
            if ($paginator->total() <= $paginator->perPage() * $paginator->currentPage()) {
                $txt .= $paginator->total();
            } else {
                $txt .= $paginator->currentPage() * $paginator->perPage();
            }
        }

        $txt .= ' из ' . $paginator->total();

        return $txt;
    }
}

if (!function_exists('workSchedule')) {
    function workSchedule(Profile $profile): array
    {
        if ($profile->work_schedules->count()) {
            return $profile->work_schedules->toArray();
        }

        return [];
    }
}

if (!function_exists('workTimeToday')) {
    function workTimeToday(Profile $profile, string $time = 'time_from')
    {
        $workTimesToday = \Illuminate\Support\Arr::where(workSchedule($profile), function ($value, $key) {
            return $value['day'] == \Illuminate\Support\Carbon::now()->dayOfWeek;
        });

        return $workTimesToday[$time] ?? null;
    }
}

if (!function_exists('availableWorkDays')) {
    function availableWorkDays(Profile $profile)
    {
        $arr = [];
        $workDays = workSchedule($profile);
        foreach ($workDays as $workDay) {
            $arr[$workDay['day']] = true;
        }

        return $arr;
    }
}

if(!function_exists('telephoneFormat')) {
    function telephoneFormat(string $telephone = null): string
    {
        return preg_replace("/[^0-9]/", '', $telephone);
    }
}

if(!function_exists('servicePrice')) {
    function servicePrice(int $service_id, $services): string
    {
        $service = \Illuminate\Support\Arr::first($services->toArray(), function ($value, $key) use ($service_id) {
            return $value['pivot']['service_id'] == $service_id;
        });

        return \Illuminate\Support\Arr::get($service, 'pivot.price') ?? '';
    }
}

if(!function_exists('userBalance')) {
    function userBalance(App\User $user = null)
    {
        if($user) {
            return $user->balance();
        }

        if(Auth::check()) {
            return \Illuminate\Support\Facades\Auth::user()->balance();
        }

        return 0;
    }
}

if(!function_exists('subDomainUrl')) {
    function subDomainUrl(string $prefix, string $path = null)
    {
        return \App\Facades\Domain::subDomainUrl($prefix, $path);
    }
}

if(!function_exists('getCityNameByDomain')) {
    function getCityNameByDomain(bool $withCountry = false)
    {
        $cityName = '';
        $city = \App\Facades\Domain::getDomainCity();
        if($withCountry) {
            $cityName .= $city->region->country->name . ', ';
        }

        $cityName .= $city->name;
        return $cityName;
    }
}

if(!function_exists('defaultCountry')) {
    function defaultCountry(string $field = null)
    {
        $country = Session::has('localisation') ? Session::get('localisation')['city']->region->country : \App\Country::first();
        return $field ? $country->$field : $country;
    }
}

if(!function_exists('hasSection')) {
    function hasSection(string $section)
    {
        return \Illuminate\Support\Facades\View::hasSection($section);
    }
}

if (!function_exists('translate')) {
    function translate(string $type, string $key = null, int $type_id = null, int $city_id = null, int $country_id = null, $default = null, bool $useSession = true, bool $useCache = true)
    {
        $translate = new \App\Services\Translate();
        return $translate->getTranslation($type, $key, $type_id, $city_id, $country_id, $default, $useSession, $useCache);
    }
}

if(!function_exists('orderStatus')) {
    function orderStatus(int $status_id) : string
    {
        switch ($status_id) {
            case 1:
                $str = 'В обработке';
                break;
            case 3:
                $str = 'Выполнен';
                break;
            default:
                $str = 'Отменен';
        }

        return $str;
    }
}

if(!function_exists('findArrayByKey')) {
    function findArrayByKey(array $arr, string $findKey, array $executeKeys)
    {
        $result = [];

        foreach ($arr as $k => $v) {
            if (is_array($arr[$k])) {
                $second_result = findArrayByKey($arr[$k], $findKey, $executeKeys);
                $result = array_merge($result, $second_result);
                continue;
            }
            if ($k === $findKey) {
                foreach ($executeKeys as $val){
                    $result[] = $arr[$val];
                }

            }
        }
        return $result;
    }
}

if(!function_exists('ticketComplaintSubject')) {
    function ticketComplaintSubject($default = null)
    {
        $settings = setting('subjects', 'tickets');
        $complaint = \Illuminate\Support\Arr::where($settings, function ($value, $key) {
            return !empty($value['complaint']);
        });

        $result = \Illuminate\Support\Arr::flatten(data_get($complaint, '*.name', []));

        return !empty($result[0]) ? $result[0] : $default;
    }
}

if(!function_exists('canonicalURL')) {
    function canonicalURL()
    {
        return \Illuminate\Support\Facades\URL::current();
    }
}

if(!function_exists('advertisingBannerEditable')) {
    function advertisingBannerEditable(\App\AdvertisingBanner $banner)
    {
        return (!$banner->active && Auth::user()->level() < 3) || $banner->date_finish < \Illuminate\Support\Carbon::today() || Auth::user()->level() > 3;
    }
}

if(!function_exists('mainDomain')) {
    function mainDomain(): bool
    {
        $domain = parse_url(\Illuminate\Support\Facades\URL::current(), PHP_URL_HOST);
        return count(explode('.', $domain)) < 3;
    }
}

if(!function_exists('telephone')) {
    function telephone($telephone)
    {
        if($telephone instanceof User || $telephone instanceof Profile) {
            $telephone = telephoneInstance($telephone);
        }

        if(empty($telephone)) {
            return null;
        }

        return $telephone->telephone_code . $telephone->telephone;
    }
}

if(!function_exists('telephoneInstance')) {
    function telephoneInstance($user)
    {
        $telephone = null;

        if($user instanceof User) {
            $telephone = $user->telephones->where('main', 1)->first();
        } else if($user instanceof Profile) {
            $telephone = $user->telephone;
            if(empty($telephone)) {
                return telephoneInstance($user->user);
            }
        }

        return $telephone;
    }
}
