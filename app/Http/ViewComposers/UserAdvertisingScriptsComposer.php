<?php
namespace App\Http\ViewComposers;

use App\Facades\SalonAdvertising;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserAdvertisingScriptsComposer
{
    public function compose(View $view)
    {
        $view->with('unAvailableDays', SalonAdvertising::getUnAvailableDays(Auth::user()->city));
    }
}