<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\Facades\Domain;
use App\Service;
use App\Traits\TranslationTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class ServiceController extends Controller
{
    use TranslationTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title' => 'Виды услуг',
            'services' => Service::paginate(25)
        ];

        return view('admin.services.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = [
            'title' => 'Новая услуга',
            'countries' => Country::all()->load('cities'),
            'country_id' => !$request->input('country_id') && !$request->input('city_id') ? Domain::getDefaultCountry() : $request->input('country_id'),
            'city_id' => $request->input('city_id')
        ];

        return view('admin.services.create', $data);
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
        $service = Service::create($request->all());
        $this->insertTranslation($request->input('translation'), $service->id, $request->input('city_id'), $request->input('country_id'));

        return redirect()->route('admin.services.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param \App\Service $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Service $service)
    {
        $data = [
            'title' => 'Редактирование услуги',
            'service' => $service,
            'countries' => Country::all()->load('cities'),
            'country_id' => !$request->input('country_id') && !$request->input('city_id') ? Domain::getDefaultCountry() : $request->input('country_id'),
            'city_id' => $request->input('city_id')
        ];

        return view('admin.services.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        $this->validateForm($request);
        $service->update($request->all());
        $this->insertTranslation($request->input('translation'), $service->id, $request->input('city_id'), $request->input('country_id'));

        return redirect()->route('admin.services.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Service $service)
    {
        $json['success'] = false;
        try {
            $service->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Cant remove service', ['message' => $e->getMessage(), 'service' => $service]);
        }

        return Response::json($json);
    }

    private function validateForm(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255'
        ]);
    }
}
