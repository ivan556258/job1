<?php

namespace App\Traits;

use App\Facades\Advertising;
use Illuminate\Support\Carbon;

trait StatusesTrait
{

    public function isNew()
    {
        $date = Carbon::now()->addDays($this->getTime());

        return $this->getAttribute('created_at') >= $date;
    }

    public function isTop()
    {
        return in_array($this->id, data_get(Advertising::getProfiles(), '*.id'));
    }

    public function isValid()
    {
        return $this->getAttribute('active') && !$this->getAttribute('block') && $this->image->active;
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    public function getTime()
    {
        return config('general.time.addDays');
    }
}