<?php

namespace App\Http\Controllers;

use App\Facades\Advertising;
use App\Facades\ProfileService;
use App\Profile;

class IndexController extends Controller
{
    public function index()
    {
        $data = [
            'meta_title' => translate('index', 'meta_title') . ' --- массаж в городе ' . getCityNameByDomain(),
            'meta_description' => translate('index', 'meta_description'),
            'meta_keywords' => translate('index', 'meta_keywords'),
            'advertisingGirls' => Advertising::getProfiles(),
            'newestGirl' => ProfileService::getNewestProfiles(),
            'totalGirls' => ProfileService::getCountOfProfiles()
        ];

        return view('front.index', $data);
    }
}
