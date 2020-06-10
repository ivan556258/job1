<?php

namespace App\Http\Middleware;

use App\City;
use App\Facades\Domain;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Subdomain
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
        \Log::info('Subdomain middleware start');
        \Log::info('Subdomain middleware. UserAgent: ' . $request->userAgent() . ' IP: ' . $request->ip());
        if(count(explode('.', $request->server('HTTP_HOST')))>3){
           abort(404);// return redirect('https://masssage.ru');
        }
        if(Session::has('localisation') && !empty(Session::get('localisation')['city']) && Session::get('localisation')['city'] instanceof City) {
            $sessionLocalisation = Session::get('localisation');
            \Log::info('Subdomain middleware. Has session', ['session' => $sessionLocalisation]);
            if(Domain::checkCityId($sessionLocalisation['city']->id)) {
                \Log::info('Subdomain middleware. City is real.');
                $localisation = $this->getLocalisation($sessionLocalisation['city'], session('redirected', false));
            } else {
                Session::forget('localisation');
                $city = Domain::getDomainCity();
                \Log::info('Subdomain middleware. City is not real.', ['city' => $city]);
                if($city) {
                    $localisation = $this->getLocalisation($city, session('redirected', false));
                }
            }
        } else {
            $subDomain = Domain::getSubDomainPrefix();
            \Log::info('Subdomain middleware. Client has not localisation.', ['subdomain' => $subDomain]);

            if(in_array($request->userAgent(), config('domain.ignore.UserAgents')) || in_array($request->ip(), config('domain.ignore.IPs'))) {
                $localisation = $this->getLocalisation(Domain::getDomainCity());
                \Log::info('Subdomain middleware. Middleware ignore client\'s GEO position. User-Agent: ' .$request->userAgent());
            } elseif(!$subDomain) {
                $city = Domain::getDomainCityByIp();
                \Log::info('Subdomain middleware. City by IP is:', ['city' => $city]);

                if($city) {
                    $localisation = $this->getLocalisation($city, true);
                    if(!Domain::checkCityId($city->id)) {
                        $redirect = Domain::subDomainUrl($city->prefix, $request->getPathInfo());
                    }
                    \Log::info('Subdomain middleware. City was found by IP.', ['localisation' => $localisation, 'redirect' => $redirect ?? '']);
                }

            } else {
                $city = City::wherePrefix($subDomain)->firstOrFail();
                $localisation = $this->getLocalisation($city, true);
                \Log::info('Subdomain middleware. Subdomain\'s city is:', ['city' => $city, 'localisation' => $localisation]);
            }
        }

        if(!isset($localisation)) {
            $localisation = $this->getLocalisation(Domain::getDefaultCity());
            if(Domain::getSubDomainPrefix()) {
                $redirect = config('app.url');
            }

            \Log::info('City was not found', ['localisation' => $localisation, 'redirect' => $redirect ?? '']);
        }

        App::setLocale($localisation['locale']);
        Session::put('localisation', $localisation);
        \Log::info('Subdomain middleware. Save session.', ['session' => Session::get('localisation')]);

        if(isset($redirect)) {
            \Log::info('Subdomain middleware. Client will be redirect.', ['redirect' => $redirect]);
            return redirect($redirect)->with(['redirected' => true]);
        }

        \Log::info('Subdomain middleware. Middleware finish');

        return $next($request);
    }

    private function getLocalisation(City $city, bool $firstVisit = false):array
    {
        $locale = $city->region->country->locale;
        $city->region->country->load(['currency']);
        $localisation = [
            'city' => $city,
            'locale' => $locale,
            'firstVisit' => $firstVisit
        ];

        return $localisation;
    }
}
