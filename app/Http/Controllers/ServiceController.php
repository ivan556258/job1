<?php

namespace App\Http\Controllers;

use App\Facades\Advertising;
use App\Profile;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ServiceController extends Controller
{
    public function index()
    {
        $data = [
            'services' => Service::all()
        ];

        return view('front.services.index', $data);
    }

    public function show(Request $request, Service $service)
    {
        $profiles = Profile::whereHas('services', function ($query) use ($service) {
            return $query->where('service_id', $service->id);
        })->with(config('relations.girl-card'));

        if ((!$request->input('page') || $request->input('page') == 1)) {
            $topProfiles = Advertising::getProfiles();
        }

        if (isset($topProfiles)) {
            $filteredProfiles = $profiles->get(['id']);
            $topProfiles = $topProfiles->whereIn('id', data_get($filteredProfiles, '*.id'));

            $profiles->whereNotIn('id', data_get($topProfiles, '*.id'));
        }

        $data = [
            'meta_title' => translate('service', 'meta_title', $service->id) . ' --- массаж в городе ' . getCityNameByDomain(),
            'meta_description' => translate('service', 'meta_description', $service->id),
            'meta_keywords' => translate('service', 'meta_keywords', $service->id),
            'service' => $service,
            'topProfiles' => $topProfiles ?? [],
            'profiles' => $profiles->paginate(20)
        ];

        return view('front.services.show', $data);
    }
}
