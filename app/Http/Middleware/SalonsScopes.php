<?php

namespace App\Http\Middleware;

use App\Salon;
use App\SalonImage;
use App\Scopes\SalonActiveScope;
use App\Scopes\SalonCityScope;
use App\Scopes\SalonHasImageScope;
use App\Scopes\SalonImagesScope;
use App\Scopes\SalonVideoScope;
use Closure;

class SalonsScopes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Salon::addGlobalScope(new SalonCityScope());
        Salon::addGlobalScope(new SalonHasImageScope());
        Salon::addGlobalScope(new SalonActiveScope());
        SalonImage::addGlobalScope(new SalonImagesScope());
        SalonImage::addGlobalScope(new SalonVideoScope());
        return $next($request);
    }
}
