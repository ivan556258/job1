<?php


namespace App\Observers;


use App\AdvertisingBanner;
use App\Services\SalonAdvertising;

class UserAdvertisingObserver extends Observer
{
    private $cacheName;
    
    public function __construct()
    {
        $this->cacheName = SalonAdvertising::$CACHE_NAME;
    }

    public function created(AdvertisingBanner $advertisingBanner)
    {
        $this->clearCache($this->cacheName .$this->getCacheName($advertisingBanner));
    }

    public function updated(AdvertisingBanner $advertisingBanner)
    {
        $this->clearCache($this->cacheName .$this->getCacheName($advertisingBanner));
    }

    public function deleted(AdvertisingBanner $advertisingBanner)
    {
        $this->clearCache($this->cacheName . $this->getCacheName($advertisingBanner));
    }

    private function getCacheName(AdvertisingBanner $advertisingBanner) :string
    {
        $cacheName = '';
        if($advertisingBanner->city_id) {
            $cacheName = 'city.' . $advertisingBanner->city_id;
        } else if($advertisingBanner->country_id) {
            $cacheName = 'country.' . $advertisingBanner->country_id;
        }

        return $cacheName;
    }

}