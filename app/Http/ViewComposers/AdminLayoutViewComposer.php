<?php


namespace App\Http\ViewComposers;


use App\AdvertisingBanner;
use App\ProfileImage;
use App\SalonImage;
use App\Ticket;
use Illuminate\View\View;

class AdminLayoutViewComposer
{
    public function compose(View $view)
    {
        $profilesHasAction = ProfileImage::notConfirmedImages()->count();
        $salonsHasAction = SalonImage::notConfirmedImages()->count();
        $ticketsHasAction = Ticket::where('status_id', '!=', 3)->count();
        $advertisingBannersHasAction = AdvertisingBanner::whereActive(0)->count();

        $view->with('profilesHasAction', $profilesHasAction);
        $view->with('salonsHasAction', $salonsHasAction);
        $view->with('ticketsHasAction', $ticketsHasAction);
        $view->with('advertisingBannersHasAction', $advertisingBannersHasAction);
    }
}