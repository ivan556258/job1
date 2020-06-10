<?php


namespace App\Observers;


use App\Salon;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class SalonObserver
{
    public function creating(Salon $salon)
    {
        $salon->advantages = $this->filterAdvantages($salon->advantages);

        $salon->elevated_at = Carbon::now();
    }

    public function updating(Salon $salon)
    {
        $salon->advantages = $this->filterAdvantages($salon->advantages);
    }

    private function filterAdvantages(array $advantages)
    {
        return Arr::where($advantages, function ($value, $key) {
            return !empty($value);
        });
    }
}