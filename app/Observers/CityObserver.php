<?php
namespace App\Observers;

use App\City;

class CityObserver extends Observer
{
    public function created(City $city)
    {
        if($city->general) {
            $this->clearCache('domain.default.city');
        }
    }

    public function updated(City $city)
    {
        $this->clearCache('domain.default.city');
    }

    public function deleted(City $city)
    {
        $this->clearCache('domain.default.city');
    }
}