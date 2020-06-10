<?php
namespace App\Observers;

use App\Country;

class CountryObserver extends Observer
{
    public function created(Country $country)
    {
        $this->clearCache('all.countries');
    }

    public function updated(Country $country)
    {
        $this->clearCache('all.countries');
    }

    public function deleted(Country $country)
    {
        $this->clearCache('all.countries');
    }
}