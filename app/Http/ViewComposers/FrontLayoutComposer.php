<?php
namespace App\Http\ViewComposers;

use App\Facades\Domain;
use App\Facades\ProfileFilters;
use App\Facades\ProfileService;
use App\Metro;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FrontLayoutComposer
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function compose(View $view)
    {
        $city_id = Domain::getCurrentCityId();
        if(Cache::has('services')) {
            $services = Cache::get('services');
        } else {
            $services = Service::all();
            Cache::forever('services', $services);
        }

        if(Cache::has('metro.stations.city.'.$city_id)) {
            $metros = Cache::get('metro.stations.city.'.$city_id);
        } else {
            $metros = Metro::whereCityId($city_id)->get();
            Cache::forever('metro.stations.city.'.$city_id, $metros);
        }

        $view->with('metroStations', $metros);
        $view->with('allServices', $services);
        $view->with('filtersHeader', ProfileFilters::getFiltersVariables($this->request));
    }
}