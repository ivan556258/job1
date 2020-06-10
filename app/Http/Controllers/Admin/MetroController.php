<?php

namespace App\Http\Controllers\Admin;

use App\City;
use App\Country;
use App\Http\Controllers\Controller;
use App\Metro;
use App\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MetroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(City $city, Country $country, Region $region)
    {
        $data = [
            'title' => 'Метро',
            'country' => $country,
            'region' => $region,
            'city' => $city,
            'metros' => $city->metros()->paginate(25)
        ];

        return view('admin.metros.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(City $city, Country $country, Region $region)
    {
        $data = [
            'title' => 'Добавить станцию',
            'city' => $city,
            'countries' => Country::all(),
            'regions' => $city->region->country->regions,
            'cities' => $city->region->cities,
            'region' => $region,
            'country' => $country
        ];

        return view('admin.metros.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request);

        Metro::create($request->all());

        return redirect()->route('admin.metros.index', [$request->input('city_id'), $request->input('country_id'), $request->input('region_id')]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Metro  $metro
     * @return \Illuminate\Http\Response
     */
    public function edit(Metro $metro, City $city, Country $country, Region $region)
    {
        $data = [
            'title' => 'Редактирование метро',
            'metro' => $metro,
            'city' => $city,
            'country' => $country,
            'region' => $region,
            'countries' => Country::all(),
            'regions' => $metro->city->region->country->regions,
            'cities' => $metro->city->region->cities
        ];

        return view('admin.metros.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Metro  $metro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Metro $metro)
    {
        $this->validateForm($request);
        $metro->update($request->all());

        return redirect()->route('admin.metros.index', [$request->input('city_id'), $request->input('country_id'), $request->input('region_id')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Metro  $metro
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Metro $metro)
    {
        $json['success'] = false;

        try {
            $metro->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Cant remove metro', ['message' => $e->getMessage(), 'metro' => $metro]);
        }

        return Response::json($json);
    }

    private function validateForm(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'country_id' => 'required',
            'region_id' => 'required',
            'city_id' => 'required'
        ]);
    }
}
