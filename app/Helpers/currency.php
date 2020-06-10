<?php
if(!function_exists('convertCurrency')) {
    function convertCurrency(float $amount, \App\Country $country = null, bool $icon = true, bool $reverse = false)
    {
        if(!$country) {
            $country = currencyCountry();
        }

        if(!$country->currency_id) {
            if($icon) {
                $amount .= ' ' . currencyIcon($country, $reverse);
            }

            return $amount;
        }

        $currency = $country->currency;

        $price = \Grechanyuk\CentralBankCurrency\Facades\CentralBankCurrency::convert($currency->iso_code, $amount, $reverse);

        if(!$reverse) {
            $price = ceil($price);
        }

        if($icon) {
            $price .= ' ' . currencyIcon($country, $reverse);
        }

        return $price;
    }
}

if(!function_exists('currencyIcon')) {
    function currencyIcon(\App\Country $country = null, bool $reverse = false): string
    {
        if(!$country && !$reverse) {
            $country = currencyCountry();
        }

        if(!$reverse) {
            $currency = $country->currency;
        }

        $icon = '₽';

        if(isset($currency) && !empty($currency->icon)) {
            $icon = $currency->icon;
        }

        return $icon;
    }
}

if(!function_exists('currencyCountry')) {
    function currencyCountry()
    {
        if(Auth::check() && Auth::user()->city_id) {
            //TODO Стирать кэш при обновлении профиля
            if(Cache::has('country.user.'.Auth::id())) {
                $country = Cache::get('country.user.'.Auth::id());
            } else {
                $user = Auth::user();
                $country = $user->city->region->country;
                Cache::put('country.user.'.Auth::id(), $country, 8600);
            }

        } else {
            $city = \App\Facades\Domain::getDomainCity();
            $country = $city->region->country;
        }

        return $country;
    }
}