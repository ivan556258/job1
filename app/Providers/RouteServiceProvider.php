<?php

namespace App\Providers;

use App\Profile;
use App\Salon;
use App\Scopes\ProfileCityScope;
use App\Scopes\ProfileHasImageScope;
use App\Scopes\ProfilesActiveScope;
use App\Scopes\ProfilesBlockScope;
use App\Scopes\SalonActiveScope;
use App\Scopes\SalonCityScope;
use App\Scopes\SalonHasImageScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::bind('accountProfile', function ($value) {
            return Auth::user()->profiles()->withoutGlobalScopes()
                ->whereKey($value)->firstOrFail();
        });

        Route::bind('frontProfile', function ($value) {
            Profile::addGlobalScope(new ProfileHasImageScope);
            Profile::addGlobalScope(new ProfilesActiveScope);
            Profile::addGlobalScope(new ProfilesBlockScope);
            Profile::addGlobalScope(new ProfileCityScope);

            return Profile::whereKey($value)->firstOrFail();
        });

        Route::bind('frontSalon', function ($value) {
            Salon::addGlobalScope(new SalonCityScope());
            Salon::addGlobalScope(new SalonHasImageScope());
            Salon::addGlobalScope(new SalonActiveScope());

            return Salon::whereKey($value)->firstOrFail();
        });

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
