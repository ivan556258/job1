<?php
namespace App\Observers;

use App\Metro;

class MetroObserver extends Observer
{
    public function created(Metro $metro)
    {
        $this->clearCache('metro.stations.city.' . $metro->city_id);
    }

    public function updated(Metro $metro)
    {
        $this->clearCache('metro.stations.city.' . $metro->city_id);
    }

    public function deleted(Metro $metro)
    {
        $this->clearCache('metro.stations.city.' . $metro->city_id);
    }
}