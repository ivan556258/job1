<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\Traits\TranslationTrait;
use Grechanyuk\CentralBankCurrency\Facades\CentralBankCurrency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class CountryController extends Controller
{
    use TranslationTrait;

    public function index()
    {
        $data = [
            'title' => 'Страны',
            'countries' => Country::all()
        ];

        return view('admin.countries.index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Добавить страну',
            'currencies' => CentralBankCurrency::getCurrenciesList()
        ];

        return view('admin.countries.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validateForm($request);
        $country = Country::create($request->all());
        $this->insertTranslation($request->input('translation'), null, null, $country->id);

        return redirect()->route('admin.countries.index');
    }

    public function edit(Country $country)
    {
        $data = [
            'title' => 'Редактирование страны',
            'country' => $country,
            'currencies' => CentralBankCurrency::getCurrenciesList()
        ];

        return view('admin.countries.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Country $country
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Country $country)
    {
        $this->validateForm($request);
        $country->update($request->all());
        $this->insertTranslation($request->input('translation'), null, null, $country->id);

        return redirect()->route('admin.countries.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Country $country
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Country $country)
    {
        $json['success'] = true;

        try {
            $country->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Cant remove country', ['message' => $e->getMessage(), 'country' => $country]);
        }

        return Response::json($json);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cities(Request $request) {
        $json['success'] = false;

        if($request->has('country_id')) {
            $country = Country::whereKey($request->input('country_id'))->first();
            if($country) {
                $json['success'] = true;
                $json['result'] = $country->regions;
            }
        }

        return Response::json($json);
    }

    private function validateForm(Request $request)
    {
        $v = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'telephone_code' => 'required|numeric',
            'icon' => 'required',
            'translation.index.meta_title' => 'required|string|max:255',
            'translation.index.meta_description' => 'required|string|max:255',
            'translation.index.meta_keywords' => 'nullable|string|max:255'
        ]);

        $v->validate();
    }
}
