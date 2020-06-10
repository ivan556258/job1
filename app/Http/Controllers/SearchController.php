<?php

namespace App\Http\Controllers;

use App\Area;
use App\City;
use App\Country;
use App\Metro;
use App\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SearchController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function searchCity(Request $request)
    {
        $json['cities'] = [];

        if ($request->has('city')) {
            $cities = City::searchCity($request->input('city'));

            if ($request->has('parents')) {
                $cities->with(['region', 'region.country']);
            }

            $json['cities'] = $cities->get();
        }

        return Response::json($json);
    }

    public function searchCityByRegion(Region $region)
    {
        return Response::json(['result' => $region->cities]);
    }

    public function searchRegionByCountry(Country $country)
    {
        return Response::json(['result' => $country->regions]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function searchMetro(Request $request)
    {
        $json['metros'] = [];

        if ($request->has('metro') && $request->has('city_id')) {
            $json['metros'] = Metro::searchMetro($request->input('metro'), $request->input('city_id'))->get();
        }

        return Response::json($json);
    }

    public function searchArea(Request $request)
    {
        $json['areas'] = [];

        if ($request->has('area') && $request->has('city_id')) {
            $json['areas'] = Area::searchArea($request->input('area'), $request->input('city_id'))->get();
        }

        return Response::json($json);
    }
}
