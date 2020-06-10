<?php
namespace App\Services;

use App\City;
use App\Country;
use App\Region;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Torann\GeoIP\Facades\GeoIP;

class Domain
{
    private $defaultCity;
    private $defaultCountry;
    
    public function __construct()
    {
        if(Cache::has('domain.default.city')) {
            $this->defaultCity = Cache::get('domain.default.city');
        } else {
            $this->defaultCity = City::whereGeneral(1)->first();
            if(!$this->defaultCity) {
                $this->defaultCity = City::first();
            }

            Cache::forever('domain.default.city', $this->defaultCity);
        }

        if(Cache::has('domain.default.country')) {
            $this->defaultCountry = Cache::get('domain.default.country');
        } else {
            if($this->defaultCity) {
                $this->defaultCountry = $this->defaultCity->region->country_id;
            } else {
                $this->defaultCountry = Country::first();
            }

            Cache::forever('domain.default.country', $this->defaultCountry);
        }
    }

    //Получает префикс субдомена, либо даст null, если это не субдомен
    public function getSubDomainPrefix()
    {
        $subDomain = null;
        $domain = explode('.', Request::getHost());
        if(count($domain) > 2) {
            $subDomain = $domain[0];
        }

        return $subDomain;
    }

    //Создает субдоменную ссылку с указанным путем
    public function subDomainUrl($prefix = null, string  $path = null): string
    {
        if(!$prefix) {
            return config('app.url') . $path;
        }

        $domain = explode('.', Request::getHost());

        if(count($domain) > 2) {
            unset($domain[0]);
        }

        return Request::getScheme() . '://' . $prefix . '.' . implode('.', $domain) . $path;
    }

    //Вернет объект App\City текущего домена
    public function getDomainCity()
    {
		//dd(Session::get('localisation')['city']);
        if(Session::has('localisation')) {
          //  return Session::get('localisation')['city'];
        }

        $prefix = $this->getSubDomainPrefix();

        if(!$prefix) {
            return $this->getDefaultCity();
        }

        return City::wherePrefix($prefix)->firstOrFail();
    } 

    //Возвращает ID города на текущем домене
    public function getCurrentCityId()
    {
        if(Session::has('localisation')) {
            return Session::get('localisation')['city']->id;
        }

        return $this->getDomainCity()->id;
    }

    //Получает город по IP
    public function getDomainCityByIp()
    {
        $city = null;
        $location = GeoIP::getLocation();
        $country = Country::whereName($location->getAttribute('country'))->with(['currency'])->first();
        if($country) {
            $cities = $country->cities()->where('cities.name', $location->getAttribute('city'))->get();

            if($cities->count() > 1) {
                $region = Region::whereName($location->getAttribute('state_name'))->first();
                if($region) {
                    $city = $region->cities()->where('cities.name', $location->getAttribute('city'))->first();
                }
            } elseif($cities->count() == 1) {
                $city = $cities->first();
            }
        }

        return $city;
    }

    //Проверяет находится ли пользователб на том домене, который прописан у него в сессии
    public function checkCityId(int $city_id): bool
    {
        $city = City::whereKey($city_id)->first();

        if(!$city) {
            return false;
        }

        $subDomain = $this->getSubDomainPrefix();
        if($subDomain) {
            return $city->prefix == $subDomain;
        }

        return $city_id == $this->getDefaultCity()->id;
    }

    /**
     * @return mixed
     */
    public function getDefaultCity()
    {
        return $this->defaultCity;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function getDefaultCountry()
    {
        return $this->defaultCountry;
    }
}