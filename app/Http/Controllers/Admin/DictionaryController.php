<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\Traits\TranslationTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class DictionaryController extends Controller
{
    use TranslationTrait;

    public function index()
    {
        $routes = Route::getRoutes();

        $routes = Arr::where($routes->getRoutes(), function ($value, $key) {
            return !empty($value->action['pageCode']);
        });

        $data = [
            'title' => 'Словари',
            'staticPages' => $routes
        ];

        return view('admin.dictionaries.index', $data);
    }

    public function edit(Request $request, string $pageCode)
    {
        $countries = Country::with(['cities'])->get();
        $data = [
            'title' => 'Редактирование контента',
            'pageCode' => $pageCode,
            'city_id' => $request->input('city_id'),
            'countries' => $countries,
            'country_id' => !$request->input('country_id') && !$request->input('city_id') ? $countries->first()->id : $request->input('country_id', $request->input('country_id'))
        ];

        return view('admin.dictionaries.edit', $data);
    }

    public function update(Request $request)
    {
        $this->insertTranslation($request->input('translation'), null, $request->input('city_id'), $request->input('country_id'));

        return redirect()->route('admin.dictionary.index');
    }
}
