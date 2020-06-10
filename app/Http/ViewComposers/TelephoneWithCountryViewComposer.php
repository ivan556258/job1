<?php


namespace App\Http\ViewComposers;


use App\Country;
use Illuminate\View\View;

class TelephoneWithCountryViewComposer
{
    public function compose(View $view)
    {
        $view->with('countries', Country::all());
    }
}