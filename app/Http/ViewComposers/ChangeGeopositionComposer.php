<?php
namespace App\Http\ViewComposers;

use App\Country;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ChangeGeopositionComposer
{
    public function compose(View $view)
    {
        if(Cache::has('all.countries')) {
            $countries = Cache::get('all.countries');
        } else {
            $countries = Country::all();
            Cache::forever('all.countries', $countries);
        }

        $view->with('countriesHeader', $countries);
    }
}