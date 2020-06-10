<?php

namespace App\Http\Controllers\Admin;

use App\City;
use App\Country;
use App\Facades\CityFilterFacade;
use App\Http\Controllers\Controller;
use App\Region;
use App\Traits\TranslationTrait;
use Grechanyuk\MetroList\Entities\MetroLine;
use Grechanyuk\MetroList\Facades\MetroList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use \Grechanyuk\MetroList\Entities\City as CityHH;

class CityController extends Controller
{
    use TranslationTrait;

    /**
     * Display a listing of the resource.
     *
     * @param Country $country
     * @param Region|null $region
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, Country $country, Region $region = null)
    {
        if($region) {
            $cities = $region->cities();
        } else {
            $cities = $country->cities();
        }

        $data = [
            'cities' => CityFilterFacade::applyFilters($request, $cities)->paginate(25),
            'cityFilters' => CityFilterFacade::getFiltersVariables($request),
            'country' => $country,
            'region' => $region
        ];

        return view('admin.cities.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Country $country
     * @param Region|null $region
     * @return \Illuminate\Http\Response
     */
    public function create(Country $country, Region $region = null)
    {
        $data = [
            'title' => 'Добавить город',
            'country' => $country,
            'region' => $region,
            'countries' => Country::all(),
            'regions' => $country->regions
        ];

        return view('admin.cities.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request);
        $city = City::create($request->all());
        $this->insertTranslation($request->input('translation'), null, $city->id);

        return redirect()->route('admin.cities.index', [$request->input('country_id'), $request->input('region_id')]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\City $city
     * @return \Illuminate\Http\Response
     */
    public function edit(City $city)
    {
        $data = [
            'title' => 'Редактирование города',
            'city' => $city,
            'countries' => Country::all(),
            'regions' => $city->region->country->regions
        ];

        return view('admin.cities.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\City $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $city)
    {
        $this->validateForm($request, $city->id);
        $city->update($request->all());
        $this->insertTranslation($request->input('translation'), null, $city->id);

        return redirect()->route('admin.cities.index', [$request->input('country_id'), $request->input('region_id')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\City $city
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(City $city)
    {
        $json['success'] = false;

        try {
            $city->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Cant remove city', ['message' => $e->getMessage(), 'city' => $city]);
        }

        return Response::json($json);
    }

    public function setMetro(Request $request, City $city)
    {
        $json['success'] = false;

        if($request->has('area_id')) {
            $metros = MetroList::getMetroList($request->input('area_id'), $city->region->country->name);
        } else {
            $metros = MetroList::getMetrosListByCityName($city->name, $city->region->country->name);
        }

        if($metros) {
            if(!empty($metros[0]) && $metros[0] instanceof CityHH) {
                $json['cities'] = $metros;
            } else {
                $metroDeleteIds = [];
                foreach ($metros as $metro) {
                    if($metro instanceof MetroLine) {
                        if($metro->hasStations()) {
                            if($city->metros->count()) {
                                $savedStations = $city->metros;
                                foreach ($metro->getStations() as $station) {
                                    $currentMetro = $savedStations->where('name', $station['name'])
                                        ->first();
                                    $metroDeleteIds[] = $currentMetro->id;
                                    if(!$currentMetro) {
                                        $city->metros()->create($station);
                                    }
                                }
                            } else {
                                foreach ($metro->getStations() as $station) {
                                    $city->metros()->firstOrCreate([
                                        'name' => $station['name']
                                    ]);
                                }
                            }
                        }
                    }
                }

                if($metroDeleteIds) {
                    $city->metros()->whereNotIn('id', $metroDeleteIds)->delete();
                }

                $json['metroCount'] = $city->metros()->count();
            }

            $json['success'] = true;
        }

        return Response::json($json);
    }

    private function validateForm(Request $request, int $cityId = 0)
    {
        $v = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'country_id' => 'required',
            'region_id' => 'required'
        ]);

        $v->sometimes('prefix', ['required', 'max:255', 'unique:cities,prefix,' . $cityId], function ($input) {
            return empty($input->general);
        });

        $v->sometimes('general', 'unique:cities,general,' . $cityId, function ($input) {
            return !empty($input->general);
        });

        $v->validate();
    }
}
