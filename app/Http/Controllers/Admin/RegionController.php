<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Country $country
     * @return \Illuminate\Http\Response
     */
    public function index(Country $country)
    {
        $data = [
            'title' => 'Регионы',
            'country' => $country,
            'regions' => $country->regions()->paginate(25)
        ];

        return view('admin.regions.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Country $country
     * @return \Illuminate\Http\Response
     */
    public function create(Country $country)
    {
        $data = [
            'title' => 'Добавить регион',
            'country' => $country,
            'countries' => Country::all()
        ];

        return view('admin.regions.create', $data);
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
        Region::create($request->all());

        return redirect()->route('admin.regions.index', $request->input('country_id'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function edit(Region $region)
    {
        $data = [
            'title' => 'Редактирование региона',
            'region' => $region,
            'countries' => Country::all()
        ];

        return view('admin.regions.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Region  $region
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Region $region)
    {
        $this->validateForm($request);

        $region->update($request->all());

        return redirect()->route('admin.regions.index', $request->input('country_id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Region  $region
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Region $region)
    {
        $json['success'] = false;
        try {
            $region->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Cant remove region', ['message' => $e->getMessage(), 'region' => $region]);
        }

        return Response::json($json);
    }

    public function cities(Request $request) {
        $json['success'] = false;

        if($request->has('region_id')) {
            $region = Region::whereKey($request->input('region_id'))->first();
            if($region) {
                $json['success'] = true;
                $json['result'] = $region->cities;
            }
        }

        return Response::json($json);
    }

    private function validateForm(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'country_id' => 'required'
        ]);
    }
}
