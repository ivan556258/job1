<?php

namespace App\Providers;

use App\Http\ViewComposers\AccountLayoutComposer;
use App\Http\ViewComposers\AccountSelectUserRoleComposer;
use App\Http\ViewComposers\Admin\UserSearchViewComposer;
use App\Http\ViewComposers\AdminLayoutViewComposer;
use App\Http\ViewComposers\AdvertisingBannersViewComposer;
use App\Http\ViewComposers\ChangeGeopositionComposer;
use App\Http\ViewComposers\FAQLayoutComposer;
use App\Http\ViewComposers\FooterMapViewComposer;
use App\Http\ViewComposers\FrontLayoutComposer;
use App\Http\ViewComposers\StaticPagesViewComposer;
use App\Http\ViewComposers\TelephoneWithCountryViewComposer;
use App\Http\ViewComposers\UserAdvertisingScriptsComposer;
use Illuminate\Support\ServiceProvider;
use Auth;
use View;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \View::composer('layouts.front', FrontLayoutComposer::class);
        \View::composer('layouts.faq', FAQLayoutComposer::class);
        \View::composer('layouts.account', AccountLayoutComposer::class);
        \View::composer('front.partials.modals.change-geoposition-modal', ChangeGeopositionComposer::class);
        \View::composer('front.partials.modals.select-user-role-modal', AccountSelectUserRoleComposer::class);
        \View::composer('front.account.advertising.partials.scripts', UserAdvertisingScriptsComposer::class);
        \View::composer(['front.partials.advertising-banners'], AdvertisingBannersViewComposer::class);
        \View::composer('front.partials.map', FooterMapViewComposer::class);
        \View::composer(['front.profiles.index', 'front.salons.index', 'front.services.index'], StaticPagesViewComposer::class);
        \View::composer('layouts.admin', AdminLayoutViewComposer::class);
        \View::composer('partials.undefined.telephone-with-country-code', TelephoneWithCountryViewComposer::class);
        \View::composer('partials.undefined.user_search', UserSearchViewComposer::class);
        View::composer('*', function($view) {
            $user = Auth::user();     
            $view->with(['authUser' => $user]);
        });
    }
}
