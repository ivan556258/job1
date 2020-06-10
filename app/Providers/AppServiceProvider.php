<?php

namespace App\Providers;

use App\Facades\SalonAdvertising;
use App\User;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use jeremykenedy\LaravelRoles\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('telephone', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[\(\)\[\]\s\-0-9\+]{5,16}$/i', $value);
        }, trans('validation.telephone'));

        Validator::extend('more_then', function ($attribute, $value, $parameters, $validator) {
            return (int)$value >= (int)$parameters[0];
        }, trans('validation.more_then'));

        Validator::extend('less_then', function ($attribute, $value, $parameters, $validator) {
            return (int)$value <= (int)$parameters[0];
        }, trans('validation.more_then'));

        Validator::extend('latin_num_dash', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[a-z0-9_@.]+$/i', $value);
        }, trans('validation.latin_num_dash'));

        Validator::extend('hash_check', function ($attribute, $value, $parameters, $validator) {
            if(!empty($parameters[1])) {
                $value = $value . $parameters[1];
            }

            return Hash::check($value, $parameters[0]);
        }, trans('validation.hash_check'));

        Validator::extend('allowed_role_id', function($attribute, $value, $parameters, $validator) {
            $allowedRoleLevel = config('general.roles.allowed_role_registration_level', 1);
            $roles = Role::where('level', '<=', $allowedRoleLevel)->get();
            return in_array($value, Arr::pluck($roles, 'id'));
        }, trans('validation.allowed_role_id'));

        Validator::extend('count_in_array', function ($attribute, $value, $parameters, $validator) {
            $arr = Arr::where($value, function ($value, $key) use($parameters) {
                //$parameter[0] - key name; $parameter[1] - more or equally then; $parameter[2] - less or equally then
                return !empty($value[$parameters[0]]) && $value[$parameters[0]] >= $parameters[1];
            });

            return count($arr) <= $parameters[2];
        });

        Validator::extend('count_in_request', function ($attribute, $value, $parameters, $validator) {
            //$parameter[0] - key name; $parameter[1] - more or equally then; $parameter[2] - less or equally then
            $attribute = preg_replace('/.\d.(.*)/', '', $attribute);
            $request = Request::only($attribute);
            $arr = findArrayByKey($request, $parameters[0], [$parameters[0]]);

            return count($arr) >= $parameters[1] && count($arr) <=$parameters[2];
        });

        Validator::extend('availableAdvertisingBannersRange', function ($attribute, $value, $parameters, $validator) {
            //$parameters[0] - user id, $parameters[1] - field name where value: city or country; $parameters[2] - field name of day count to finish, $value - date start
            //$parameters[3] - App\AdvertisingBanner ID for ignoring this advertising

            $user = User::whereKey($parameters[0])->firstOrFail();

            $type = Request::input($parameters[1]);
            $days = Request::input($parameters[2]);

            if(is_array($type)) {
                $type = $type[0];
            }

            if(is_array($days)) {
                $days = $days[0];
            }

            switch ($type) {
                case 'city':
                    $location = $user->city;
                    break;
                case 'country':
                    $location = $user->city->region->country;
                    break;
            }

            if(!isset($location)) {
                return false;
            }

            $unAvailableDays = SalonAdvertising::getUnAvailableDays($location, (int)$parameters[3]);
            $value = Carbon::createFromFormat('d.m.Y', $value);

            $advertisingRange = Carbon::range($value, $value->copy()->addDays($days))->toArray();

            $advertisingRange = array_map(function ($value) {
                return $value->format('d.m.Y');
            }, $advertisingRange);

            return empty(array_intersect($unAvailableDays, $advertisingRange));
        }, trans('validation.availableAdvertisingBannersRange'));

        Validator::replacer('count_in_array', function ($message, $attribute, $rule, $parameters) {
            return str_replace([':count'], $parameters[1], $message);
        });

        Validator::replacer('count_in_request', function ($message, $attribute, $rule, $parameters) {
            return str_replace([':count'], $parameters[1], $message);
        });

        Carbon::macro('range', function ($start, $end) {
            return new Collection(new DatePeriod($start, new DateInterval('P1D'), $end));
        });
    }
}
